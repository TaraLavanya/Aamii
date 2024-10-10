<?php

namespace App\Http\Controllers;

use App\Jobs\SendWelcomeNotificationThorughWhatsappBotJob;
use App\Models\Address;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\EventExhibitor;
use App\Models\EventVisitor;
use App\Models\Exhibitor;
use App\Models\Product;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MigrationController extends Controller
{
    public function sendVisitorsWelcomeNotification()
    {
        $visitors = Visitor::get();

        foreach ($visitors as $visitorIndex => $visitor) {
            dispatch(new SendWelcomeNotificationThorughWhatsappBotJob($visitor))
                ->onQueue('visitor_welcome_notification')
                ->delay(now()->addSeconds(10));
            echo ($visitorIndex + 1) . " . sent to $visitor->name <br>";
        }
        // HOw to trigger the queue
        echo 'All visitors welcome notification dispatched to queue.';
        echo '<br>';
        echo 'Now run the queue worker to send the welcome notification to the visitors.';
        echo '<br>';
    }

    public function updateEventProductsInStringFormat()
    {
        $eventExhibitors = EventExhibitor::get();

        foreach ($eventExhibitors as $eventExhibitor) {

            $productIds = $eventExhibitor->products;

            $productIds = array_map(function ($productId) {
                return strval($productId);
            }, $productIds);

            $eventExhibitor->products = $productIds;
            $eventExhibitor->save();
        }

        echo 'All event exhibitors products updated to string format.';
    }

    public function updateDesignationFieldIn10TVisitorsTable()
    {
        $data = readCSV(public_path('/assets/10t-visitors-dec-23.csv'));

        $updatedCount = 0;
        foreach ($data as $visitorInfo) {
            $mobileNumber = $visitorInfo['Phone'] ?? '';
            $designation = $visitorInfo['Designation'] ?? '';

            $visitor = Visitor::where('mobile_number', $mobileNumber)->first();

            if ($visitor) {
                $visitor->designation = $designation;
                $visitor->save();
                $updatedCount++;
            }
        }

        echo 'Total ' . $updatedCount . ' visitors designation updated.<br>';
    }

    public function registeringVisitorsWhoMakesAppointmentsWithoutRegister()
    {
        $eventId = 12;

        $visitorIds = Appointment::select('visitor_id')->where('event_id', $eventId)
            ->where('visitor_id', '>', 0)
            ->groupBy('visitor_id')
            ->pluck('visitor_id')
            ->toArray();

        foreach ($visitorIds as $visitorId) {
            $exitsInEvent = EventVisitor::where('event_id', $eventId)
                ->where('visitor_id', $visitorId)
                ->first();

            if ($exitsInEvent) {
                continue;
            }

            $visitor = Visitor::find($visitorId);
            if ($visitor) {
                $eventVisitor = new EventVisitor();
                $eventVisitor->event_id = $eventId;
                $eventVisitor->visitor_id = $visitorId;
                $eventVisitor->is_visited = 1;
                $eventVisitor->save();
            }
        }
        return 'All visitors registered successfully.';
    }

    public function updateExhibitorWebsiteUrl()
    {
        $exhibitors = Exhibitor::where('website', '!=', null)->get();
        foreach ($exhibitors as $exhibitor) {
            $meta = $exhibitor->_meta ?? null;
            if (isset($exhibitor->website) && empty($meta['website_url'])) {
                $meta['website_url'] = $exhibitor->website;
                $exhibitor->_meta = $meta;
                $exhibitor->save();
            }
        }
        return 'All exhibitors website url updated successfully.';
    }

    public function importVisitorsFromGivenFile(Request $request)
    {
        $filename = $request->file_name ?? '';
        if (empty($filename)) {
            return 'Please provide the file name.';
        }

        $data = readCSV(public_path('/assets/' . $filename));

        $totalVisitors = count($data);
        $insertedVisitorsCount = 0;
        $alreadyRegisteredCount = 0;
        $notInsertedVisitorsCount = 0;
        $registeredVisitorsCount = 0;
        $currentEventInfo = getCurrentEvent();

        foreach ($data as $visitorInfo) {
            $visitorFormattedData = [
                'salutation' => $visitorInfo['title'] ?? 'Mr',
                'name' => $visitorInfo['name'] ?? '',
                'email' => $visitorInfo['email'] ?? '',
                'mobile' => $visitorInfo['mobile'] ?? '',
                'designation' => $visitorInfo['designation'] ?? '',
                'organization' => $visitorInfo['organization'] ?? '',
                'address' => $visitorInfo['address'] ?? '',
                'city' => $visitorInfo['city'] ?? '',
                'cityname' => $visitorInfo['cityname'] ?? '',
                'state' => $visitorInfo['state'] ?? '',
                'country' => $visitorInfo['country'] ?? '',
                'pincode' => $visitorInfo['post_code'] ?? '',
                'source' => 'medicall',
                'nature_of_business' => $visitorInfo['businesstype'] ?? '',
                'reason_for_visit' => $visitorInfo['products_interested'] ?? '',
                'known_source' => $visitorInfo['knowingname'] ?? '',
            ];

            $addVisitor = $this->addVisitor($visitorFormattedData, $currentEventInfo);

            if ($addVisitor['status'] == 'success') {
                if ($addVisitor['type'] == "VISITOR_REGISTERED") {
                    $insertedVisitorsCount++;
                } else if ($addVisitor['type'] == 'VISITOR_ALREADY_EXISTS_AND_REGISTERED_FOR_EVENT') {
                    $registeredVisitorsCount++;
                } else if ($addVisitor['type'] == 'VISITOR_ALREADY_REGISTERED_FOR_EVENT') {
                    $alreadyRegisteredCount++;
                }
            } else {
                $notInsertedVisitorsCount++;
            }
        }

        return 'Total ' . $totalVisitors . ' visitors found. <br> ' . $insertedVisitorsCount . ' visitors inserted. <br> ' . $alreadyRegisteredCount . ' visitors already registered. <br> ' . $notInsertedVisitorsCount . ' visitors not inserted. <br> ' . $registeredVisitorsCount . ' visitors are registered for the event.';
    }

    public function addVisitor($visitorData, $currentEvent)
    {
        $visitor = Visitor::where('mobile_number', $visitorData['mobile'])->first();

        if ($visitor) {

            $eventVisitor = $visitor->eventVisitors()->where('event_id', $currentEvent->id ?? 0)->first();
            if ($eventVisitor) {
                return [
                    'status' => 'success',
                    'type' => 'VISITOR_ALREADY_REGISTERED_FOR_EVENT',
                    'message' => 'Visitor already registered for the event',
                ];
            }

            $visitor->eventVisitors()->create([
                'event_id' => $currentEvent->id ?? 0,
            ]);

            return [
                'status' => 'success',
                'type' => 'VISITOR_ALREADY_EXISTS_AND_REGISTERED_FOR_EVENT',
                'message' => 'Visitor already exists with mobile number ' . $visitorData['mobile'],
            ];
        }

        $username = str_replace(' ', '-', $visitorData['name']);
        $username = strtolower($username);
        $username = $username . '-' . rand(1000, 9999);

        while (Visitor::where('username', $username)->first()) {
            $username = $username . '-' . rand(1000, 9999);
        }
        $defaultPassword = config('app.default_user_password');

        $natureOfBusinessId = null;
        $natureOfBusiness = $visitorData['nature_of_business'] ?? '';
        if (!empty($natureOfBusiness)) {
            $category = Category::where('type', 'visitor_business_type')
                ->where('name', 'like', '%' . $natureOfBusiness . '%')
                ->first();

            if (!$category) {
                $category = Category::create([
                    'name' => $natureOfBusiness,
                    'type' => 'visitor_business_type',
                    'is_active' => 1,
                ]);
            }
            $natureOfBusinessId = $category->id ?? null;
        }

        $visitor = new Visitor();
        $visitor->username = $username;
        $visitor->password = Hash::make($defaultPassword);
        $visitor->salutation = $visitorData['salutation'];
        $visitor->name = $visitorData['name'];
        $visitor->mobile_number = $visitorData['mobile'];
        $visitor->email = $visitorData['email'];
        $visitor->organization = $visitorData['organization'];
        $visitor->designation = $visitorData['designation'];
        $visitor->registration_type = $visitorData['source'] ?? 'medicall';
        $visitor->category_id = $natureOfBusinessId ?? null;
        $visitor->reason_for_visit = $visitorData['reason_for_visit'];
        $visitor->known_source = $visitorData['known_source'];
        $visitor->save();

        if (!$visitor) {
            return [
                'status' => 'error',
                'type' => 'VISITOR_NOT_REGISTERED',
                'message' => 'Visitor not registered',
            ];
        }

        $visitor->address()->create([
            'address' => $visitorData['address'] ?? '',
            'pincode' => $visitorData['pincode'] ?? '',
            'city' => $visitorData['city'] ?? '',
            'state' => $visitorData['state'] ?? '',
            'country' => $visitorData['country'] ?? '',
        ]);

        $visitor->eventVisitors()->create([
            'event_id' => $currentEvent->id ?? 0,
        ]);

        return [
            'status' => 'success',
            'type' => 'VISITOR_REGISTERED',
            'message' => 'Visitor registered successfully',
        ];
    }

    public function import10TData(Request $request)
    {
        $filename = $request->filename ?? '';
        if (empty($filename)) {
            return 'Please provide the file name.';
        }

        $data = readCSV(asset('/assets/' . $filename));
        $totalVisitors = count($data);
        $insertedVisitorsCount = 0;
        $alreadyRegisteredCount = 0;
        $notInsertedVisitorsCount = 0;
        $registeredVisitorsCount = 0;

        $currentEventInfo = getCurrentEvent();

        foreach ($data as $visitorInfo) {
            $salutation = $visitorInfo['Salutation'] ?? 'Mr';

            $visitorFormattedData = [
                'salutation' => !empty($salutation) ? $salutation : 'Mr',
                'name' => $visitorInfo['Name'] ?? '',
                'email' => $visitorInfo['Email'] ?? '',
                'mobile' => $visitorInfo['Phone'] ?? '',
                'designation' => $visitorInfo['Designation'] ?? '',
                'organization' => $visitorInfo['Company'] ?? '',
                'address' => $visitorInfo['address'] ?? '',
                'city' => $visitorInfo['City'] ?? '',
                'cityname' => $visitorInfo['cityname'] ?? '',
                'state' => $visitorInfo['state'] ?? '',
                'country' => $visitorInfo['Country'] ?? '',
                'pincode' => $visitorInfo['post_code'] ?? '',
                'source' => '10t',
                'nature_of_business' => $visitorInfo['businesstype'] ?? '',
                'reason_for_visit' => $visitorInfo['Which topics or products are you eager to explore?'] ?? '',
                'known_source' => $visitorInfo['knowingname'] ?? '',
            ];

            $addVisitor = $this->addVisitor($visitorFormattedData, $currentEventInfo);

            if ($addVisitor['status'] == 'success') {
                if ($addVisitor['type'] == "VISITOR_REGISTERED") {
                    $insertedVisitorsCount++;
                } else if ($addVisitor['type'] == 'VISITOR_ALREADY_EXISTS_AND_REGISTERED_FOR_EVENT') {
                    $registeredVisitorsCount++;
                } else if ($addVisitor['type'] == 'VISITOR_ALREADY_REGISTERED_FOR_EVENT') {
                    $alreadyRegisteredCount++;
                }
            } else {
                $notInsertedVisitorsCount++;
            }
        }

        return 'Total ' . $totalVisitors . ' visitors found. <br> ' . $insertedVisitorsCount . ' visitors inserted. <br> ' . $alreadyRegisteredCount . ' visitors already registered. <br> ' . $notInsertedVisitorsCount . ' visitors not inserted. <br> ' . $registeredVisitorsCount . ' visitors are registered for the event.';
    }

    public function deleteNotExistExhibitorProducts()
    {
        $exhibitors = Exhibitor::all();
        $deletedCount = 0;

        foreach ($exhibitors as $exhibitor) {
            if ($exhibitor->exhibitorProducts) {
                foreach ($exhibitor->exhibitorProducts as $exhibitorProduct) {
                    $product = Product::find($exhibitorProduct->product_id);
                    if (!$product) {
                        $exhibitorProduct->delete();
                        $deletedCount++;
                    }
                }
            }

        }

        if ($deletedCount > 0) {
            return "$deletedCount products deleted successfully";
        } else {
            return "No products deleted";
        }
    }

    public function setVisitedAtValues()
    {
        $eventVisitors = EventVisitor::where('is_visited', 1)
            ->whereNull('visited_at')
            ->get();
        $updatedCount = 0;
        foreach ($eventVisitors as $eventVisitor) {
            $eventVisitor->visited_at = $eventVisitor->updated_at;
            $eventVisitor->save();
            $updatedCount++;
        }
        return 'Total ' . $updatedCount . ' visitors visited at values updated.';
    }

    public function migrateRegistrationTypes()
    {
        $visitors = Visitor::all();

        foreach ($visitors as $visitor) {

            foreach ($visitor->eventVisitors as $eventvisitor) {

                $eventvisitor->registration_type = $visitor->registration_type;

                $eventvisitor->save();
            }
        }
        return 'Registration Type migrated successfully';

    }

}
