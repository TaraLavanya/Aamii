<?php

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Visitor;
use App\Models\Category;
use App\Models\Exhibitor;
use App\Models\Appointment;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

if (!function_exists('getActiveUnits')) {
    function getActiveUnits()
    {
        return Category::where('is_active', 1)->where('type', 'unit')->get();
    }
}

if (!function_exists('getNameFirstChars')) {
    function getNameFirstChars($name = '')
    {
        if (empty($name)) {
            return '';
        }
        $words = explode(' ', $name);
        $chars = '';
        foreach ($words as $word) {
            $chars .= substr($word, 0, 1);
        }
        return $chars;
    }
}

if (!function_exists('getPincodeData')) {
    function getPincodeData($pincode)
    {

        $address = [
            'state' => null,
            'city' => null,
        ];
        if (isset($pincode)) {
            try {
                $response = Http::get("https://api.data.gov.in/resource/6176ee09-3d56-4a3b-8115-21841576b2f6?api-key=579b464db66ec23bdd000001cdd3946e44ce4aad7209ff7b23ac571b&format=json&filters%5Bpincode%5D=$pincode");
                if ($response->successful()) {
                    $pinCodeDatas = $response->json();

                    if ($pinCodeDatas['status'] == 'ok') {

                        $address['state'] = $pinCodeDatas['records'][0]['statename'] ?? '';
                        $address['city'] = $pinCodeDatas['records'][0]['districtname'] ?? '';
                    }
                    return $address;
                } else {
                    return $address;
                }
            } catch (ConnectionException $e) {
                return $address;
            }
        }
    }
}

if (!function_exists('getAuthData')) {
    function getAuthData()
    {
        if (Auth::guard('exhibitor')->check()) {
            return auth()->guard('exhibitor')->user();
        }

        if (Auth::guard('visitor')->check()) {

            return auth()->guard('visitor')->user();
        }

        if (Auth::guard('user')->check() || Auth::guard('web')->check()) {
            return auth()->guard('user')->check() ? auth()->guard('user')->user() : auth()->guard('web')->user();
        }

        return null;
    }
}

if (!function_exists('isOrganizer')) {
    function isOrganizer()
    {
        return !auth()->guard('visitor')->check() && !auth()->guard('exhibitor')->check();
    }
}

if (!function_exists('getRandomBackgroundColor')) {
    function getRandomBackgroundColor()
    {
        $colors = [
            'bg-blue-lt',
            'bg-azure-lt',
            'bg-indigo-lt',
            'bg-purple-lt',
            'bg-pink-lt',
            'bg-red-lt',
            'bg-orange-lt',
            'bg-yellow-lt',
            'bg-lime-lt',
            'bg-green-lt',
            'bg-teal-lt',
            'bg-cyan-lt',
            'bg-dark-lt',
            'bg-muted-lt',
        ];

        return $colors[array_rand($colors)];
    }
}

if (!function_exists('getKnownSourceData')) {
    function getKnownSourceData()
    {
        $knowSources = [
            "brochure" => "Brochure",
            "bus_panel" => "Bus Panel",
            "emailers" => "Emailers",
            "facebook_instagram" => "Facebook/Instagram",
            "field_force" => "Field Force",
            "hoardings" => "Hoardings",
            "hotline" => "Hotline",
            "i_know_medicall" => "I Know Medicall",
            "internet_search" => "Internet Search",
            "linkedin" => "Linkedin",
            "newspaper_ad" => "Newspaper Ad",
            "outdoor" => "Outdoor",
            "promoters" => "Promoters",
            "sms" => "SMS",
            "team_medicall" => "Team Medicall",
            "tele-calling" => "Tele-Calling",
            "tele-marketing" => "Tele-Marketing",
            "through_exhibitors" => "Through Exhibitors",
            "twitter" => "Twitter",
            "whatsApp" => "WhatsApp",
            "website" => "Website",
            "friend" => "Friend",
            "google" => "Google",
            "word-of-mouth" => "Word-of-mouth",
        ];
        return $knowSources;
    }
}

if (!function_exists('sendLoginOtp')) {
    function sendLoginOtp($mobileNumber, $otp, $userType = 'visitor')
    {

        if (empty($mobileNumber) || empty($otp)) {
            return ['status' => 'error', 'message' => 'Mobile number or OTP is empty'];
        }

        $url = 'https://api.engati.com/whatsapp-api/v1.0/customer/68259/bot/fad6d0cc73a045ac/template';
        if ("exhibitor" == $userType) {
            $url = 'https://api.engati.com/whatsapp-api/v1.0/customer/68259/bot/0046ad9b644d4274/template';
        }

        try {
            $payload = [
                "payload" => [
                    "name" => "1to1connect_otp",
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => strval($otp),
                                ],
                            ],
                        ],
                    ],
                    "language" => [
                        "code" => "en_US",
                        "policy" => "deterministic",
                    ],
                    "namespace" => "dd96ee84_2909_4425_86ba_8a7fb56c2d2a",
                ],
                "phoneNumber" => $mobileNumber,
            ];
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 0,
                    CURLOPT_POST => 1,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic a29ad3e1-2bb5-4126-9d44-153e96330a54-G4TG8QD",
                        "Content-Type: application/json",
                    ),
                )
            );

            $result = curl_exec($curl);
            $response = json_decode($result, true);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return ['status' => 'error', 'message' => $err];
            }

            if ($response['status']['code'] == 1000) {
                return ['status' => 'success', 'message' => 'OTP sent successfully', 'response' => $response];
            }

            return ['status' => 'error', 'message' => $response['status']['message'], 'response' => $response];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('getCurrentEvent')) {
    function getCurrentEvent()
    {
        $event = Event::where('start_date', '>=', now()->format('Y-m-d'))
            ->orWhere('end_date', '>=', now()->format('Y-m-d'))
            ->orderBy('start_date', 'asc')
            ->first();

        return $event ?? null;
    }
}

if (!function_exists('getCountries')) {
    function getCountries()
    {
        $countries =
            [
                'Afghanistan',
                'Albania',
                'Algeria',
                'Andorra',
                'Angola',
                'Antigua and Barbuda',
                'Argentina',
                'Armenia',
                'Australia',
                'Austria',
                'Azerbaijan',
                'Bahamas',
                'Bahrain',
                'Bangladesh',
                'Barbados',
                'Belarus',
                'Belgium',
                'Belize',
                'Benin',
                'Bhutan',
                'Bolivia',
                'Bosnia and Herzegovina',
                'Botswana',
                'Brazil',
                'Brunei',
                'Bulgaria',
                'Burkina Faso',
                'Burundi',
                'Cabo Verde',
                'Cambodia',
                'Canada',
                'China',
                'Colombia',
                'Comoros',
                'Egypt',
                'Finland',
                'France',
                'Gabon',
                'Gambia',
                'Georgia',
                'Germany',
                'Ghana',
                'Greece',
                'Grenada',
                'Guatemala',
                'Guinea',
                'Guinea-Bissau',
                'Guyana',
                'Haiti',
                'Honduras',
                'Hungary',
                'Iceland',
                'India',
                'Indonesia',
                'Iran',
                'Iraq',
                'Ireland',
                'Israel',
                'Italy',
                'Jamaica',
                'Japan',
                'Jordan',
                'Kazakhstan',
                'Kenya',
                'Kiribati',
                'Korea North',
                'Korea South',
                'Kosovo',
                'Kuwait',
                'Kyrgyzstan',
                'Laos',
                'Latvia',
                'Malaysia',
                'Mexico',
                'Micronesia',
                'Morocco',
                'Nepal',
                'Netherlands',
                'New Zealand',
                'Norway',
                'Oman',
                'Pakistan',
                'Palau',
                'Palestine',
                'Panama',
                'Peru',
                'Philippines',
                'Poland',
                'Portugal',
                'Qatar',
                'Romania',
                'Russia',
                'Rwanda',
                'Serbia',
                'South Africa',
                'South Sudan',
                'Spain',
                'Sri Lanka',
                'Sudan',
                'Sweden',
                'Switzerland',
                'Syria',
                'Taiwan',
                'Tajikistan',
                'Tanzania',
                'Thailand',
                'Tonga',
                'Turkey',
                'Uganda',
                'Ukraine',
                'United Arab Emirates',
                'United Kingdom',
                'United States',
                'Uruguay',
                'Uzbekistan',
                'Vanuatu',
                'Vatican City',
                'Venezuela',
                'Vietnam',
                'Yemen',
                'Zambia',
                'Zimbabwe',

            ];

        return $countries;
    }
}

if (!function_exists('sendWelcomeMessageThroughWhatsappBot')) {
    function sendWelcomeMessageThroughWhatsappBot($mobileNumber, $userType = 'visitor')
    {
        $currentEvent = getCurrentEvent();
        if (empty($mobileNumber)) {
            return ['status' => 'fail', 'message' => 'Mobile number is empty'];
        }

        $templateName = $userType == 'visitor' ? '1to1_visitor_broadcast' : '1to1_exhibitor_broadcast';
        $url = 'https://api.engati.com/whatsapp-api/v1.0/customer/68259/bot/fad6d0cc73a045ac/template';
        if ($userType == 'exhibitor') {
            $url = 'https://api.engati.com/whatsapp-api/v1.0/customer/68259/bot/fad6d0cc73a045ac/template';
        }

        try {
            $payload = [
                "payload" => [
                    "name" => $templateName,
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $currentEvent->title ?? '',
                                ],
                                [
                                    "type" => "text",
                                    "text" => strval($mobileNumber),
                                ],
                            ],
                        ],
                    ],
                    "language" => [
                        "code" => "en_US",
                        "policy" => "deterministic",
                    ],
                    "namespace" => "dd96ee84_2909_4425_86ba_8a7fb56c2d2a",
                ],
                "phoneNumber" => $mobileNumber,
            ];
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 0,
                    CURLOPT_POST => 1,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic a29ad3e1-2bb5-4126-9d44-153e96330a54-G4TG8QD",
                        "Content-Type: application/json",
                    ),
                )
            );

            $response = curl_exec($curl);

            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return ['status' => 'fail', 'message' => $err];
            } else {
                return ['status' => 'success', 'message' => 'Sent successfully', 'response' => $response];
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('sendAppointmentInitNotification')) {
    function sendAppointmentInitNotification($mobileNumber, $data = [])
    {
        $senderName = $data['senderName'] ?? '';
        $receiverName = $data['receiverName'] ?? '';
        $scheduledAt = $data['scheduledAt'] ?? '';
        if (empty($mobileNumber) || empty($senderName) || empty($receiverName) || empty($scheduledAt)) {
            return ['status' => 'fail', 'message' => 'Please fill the required fields'];
        }
        $url = 'https://api.engati.com/whatsapp-api/v1.0/customer/68259/bot/0046ad9b644d4274/template';
        $templateName = 'appointment_trigger';
        try {
            $payload = [
                "payload" => [
                    "name" => $templateName,
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $receiverName,
                                ],
                                [
                                    "type" => "text",
                                    "text" => $senderName,
                                ],
                                [
                                    "type" => "text",
                                    "text" => $scheduledAt,
                                ],
                            ],
                        ],
                    ],
                    "language" => [
                        "code" => "en_US",
                        "policy" => "deterministic",
                    ],
                    "namespace" => "dd96ee84_2909_4425_86ba_8a7fb56c2d2a",
                ],
                "phoneNumber" => $mobileNumber,
            ];
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 0,
                    CURLOPT_POST => 1,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic a29ad3e1-2bb5-4126-9d44-153e96330a54-G4TG8QD",
                        "Content-Type: application/json",
                    ),
                )
            );

            $response = curl_exec($curl);

            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return ['status' => 'fail', 'message' => $err];
            } else {
                return ['status' => 'success', 'message' => 'Sent successfully', 'response' => $response];
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('sendAppointmentStatusChangeNotification')) {
    function sendAppointmentStatusChangeNotification($mobileNumber, $userType, $data)
    {
        $senderName = $data['senderName'] ?? '';
        $receiverName = $data['receiverName'] ?? '';
        $scheduledAt = $data['scheduledAt'] ?? '';
        $status = $data['status'] ?? '';
        if (empty($mobileNumber) || empty($senderName) || empty($receiverName) || empty($scheduledAt) || empty($status)) {
            return ['status' => 'fail', 'message' => 'Please fill the required fields'];
        }

        $url = 'https://api.engati.com/whatsapp-api/v1.0/customer/68259/bot/0046ad9b644d4274/template';
        if ('visitor' == $userType) {
            $url = 'https://api.engati.com/whatsapp-api/v1.0/customer/68259/bot/0d10a092bb054b35/template';
        }
        $templateName = 'appointment_status_exhibitor';
        try {
            $payload = [
                "payload" => [
                    "name" => $templateName,
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $receiverName,
                                ],
                                [
                                    "type" => "text",
                                    "text" => $senderName,
                                ],
                                [
                                    "type" => "text",
                                    "text" => $scheduledAt,
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $status,
                                ],
                            ],
                        ],
                    ],
                    "language" => [
                        "code" => "en_US",
                        "policy" => "deterministic",
                    ],
                    "namespace" => "dd96ee84_2909_4425_86ba_8a7fb56c2d2a",
                ],
                "phoneNumber" => $mobileNumber,
            ];
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 0,
                    CURLOPT_POST => 1,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic a29ad3e1-2bb5-4126-9d44-153e96330a54-G4TG8QD",
                        "Content-Type: application/json",
                    ),
                )
            );

            $response = curl_exec($curl);


            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return ['status' => 'fail', 'message' => $err];
            } else {
                return ['status' => 'success', 'message' => 'Sent successfully', 'response' => $response];
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('sendAppointmentStatusChangeEmail')) {
    function sendAppointmentStatusChangeEmail($emailAddress, $data)
    {
        $receiverEmail = $emailAddress['receiverEmail'] ?? '';
        $senderName = $data['senderName'] ?? '';
        $receiverName = $data['receiverName'] ?? '';
        $scheduledAt = $data['scheduledAt'] ?? '';
        $status = $data['status'] ?? '';
        $appointmentId = $emailAddress['appointmentId'] ?? '';
        if (empty($receiverEmail) || empty($senderName) || empty($receiverName) || empty($scheduledAt) || empty($status)) {
            return ['status' => 'fail', 'message' => 'Please fill the required fields'];
        }
        if (in_array($status, ['Scheduled', 'Rescheduled', 'Confirmed'])) {
            $icsContent = generateICSFile($appointmentId);
            $encodedICS = base64_encode($icsContent);
        } else {
            $encodedICS = '';
        }
        $currentEvent = getCurrentEvent()->title ?? 'No event found';
        $currentEventTitle = substr($currentEvent, 0, 4) . ' ' . substr($currentEvent, 13, 19);

        try {
            $payload = [
                'from' => [
                    'address' => '1to1@medicall.in',
                ],
                'to' => [
                    [
                        'email_address' => [
                            'address' => $receiverEmail,
                            'name' => $receiverName,
                        ],
                    ],
                ],
                'subject' => 'Appointment ' . $status . ' with ' . $senderName . ' at ' . trim($currentEventTitle) . '',
                'htmlbody' => '<div><table style="width:100%;border-collapse:collapse;"><tr><th style="border:1px solid #ddd;padding:8px;text-align:left;">Event</th><td style="border:1px solid #ddd;padding:8px;text-align:left;">' . $currentEvent . '</td></tr><tr><th style="border:1px solid #ddd;padding:8px;text-align:left;">Sender Name</th><td style="border:1px solid #ddd;padding:8px;text-align:left;">' . $senderName . '</td></tr><tr><th style="border:1px solid #ddd;padding:8px;text-align:left;">Receiver Name</th><td style="border:1px solid #ddd;padding:8px;text-align:left;">' . $receiverName . '</td></tr><tr><th style="border:1px solid #ddd;padding:8px;text-align:left;">Scheduled At</th><td style="border:1px solid #ddd;padding:8px;text-align:left;">' . $scheduledAt . '</td></tr><tr><th style="border:1px solid #ddd;padding:8px;text-align:left;">Status</th><td style="border:1px solid #ddd;padding:8px;text-align:left;">' . $status . '</td></tr></table></div>',
                'attachments' => [
                    [
                        'content' => $encodedICS,
                        'name' => 'medicall' . $appointmentId . '.ics',
                        'mime_type' => 'text/calendar',
                        'content-type' => 'text/calendar; charset=utf-8',
                        'content-disposition' => 'attachment; filename=medicall' . $appointmentId . '.ics',
                    ],
                ],
            ];

            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => "https://api.zeptomail.in/v1.1/email",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => array(
                        "accept: application/json",
                        "authorization: Zoho-enczapikey PHtE6r0FQ7i+3TIt8xUC4PS7HselZoIm/+lhKQBAt95EDaRQFk1Rq40swGLirxt4A/AXQfebmoxgtbrJ5bqGLT67YGkdCWqyqK3sx/VYSPOZsbq6x00etF0Tc0DVVIfmct9p3SXTutbaNA==",
                        "cache-control: no-cache",
                        "content-type: application/json",
                    ),
                )
            );
            $result = curl_exec($curl);
            $curlError = curl_error($curl);
            curl_close($curl);

            $response = json_decode($result, true);

            if ($curlError) {
                return ['status' => 'fail', 'message' => $curlError];
            }

            return ['status' => 'success', 'message' => 'send success', 'response' => $response];
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('generateICSFile')) {
    function generateICSFile($appointmentId)
    {
        $organizerName = "Medicall";
        $organizerEmail = "1to1@medicall.in";
        $appointment = Appointment::find($appointmentId);
        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "METHOD:PUBLISH\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "SUMMARY:" . (auth()->guard('exhibitor')->check() ? $appointment->visitor->name : $appointment->exhibitor->name) . "\r\n";
        $icsContent .= "DESCRIPTION:" . $appointment->notes . "\r\n";
        $icsContent .= "ORGANIZER;CN=$organizerName:$organizerEmail\r\n";

        $icsContent .= "DTSTART:" . Carbon::parse($appointment->scheduled_at)->format('Ymd\THis') . "\r\n";
        $icsContent .= "DTEND:" . Carbon::parse($appointment->scheduled_at)->addHours(2)->format('Ymd\THis') . "\r\n";

        $icsContent .= "BEGIN:VALARM\r\n";
        $icsContent .= "ACTION:DISPLAY\r\n";
        $icsContent .= "TRIGGER:-P1DT\r\n";
        $icsContent .= "DESCRIPTION:Reminder - You have an appointment with " . (auth()->guard('exhibitor')->check() ? $appointment->visitor->name : $appointment->exhibitor->name) . "\r\n";
        $icsContent .= "END:VALARM\r\n";

        $icsContent .= "BEGIN:VALARM\r\n";
        $icsContent .= "ACTION:DISPLAY\r\n";
        $icsContent .= "TRIGGER:-PT10M\r\n";
        $icsContent .= "DESCRIPTION:Reminder - You have an appointment with " . (auth()->guard('exhibitor')->check() ? $appointment->visitor->name : $appointment->exhibitor->name) . "\r\n";
        $icsContent .= "END:VALARM\r\n";

        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR\r\n";
        $fileName = 'medicall' . $appointmentId . '.ics';
        return $icsContent;
    }
}

if (!function_exists('readCSV')) {
    function readCSV($filePath)
    {
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);
        $data = [];
        while ($row = fgetcsv($file)) {
            $data[] = array_combine($header, $row);
        }
        fclose($file);
        return $data;
    }
}

if (!function_exists('getPreviousEvents')) {
    function getPreviousEvents()
    {
        $getCurrentEventId = getCurrentEvent()->id ?? 0;
        $previousEvents = Event::where('id', '!=', $getCurrentEventId)
            ->where('start_date', '<', now()->format('Y-m-d'))
            ->where('end_date', '<', now()->format('Y-m-d'))
            ->select('id', 'title')
            ->orderBy('start_date', 'desc')
            ->get();

        return $previousEvents ?? null;
    }
}

if (!function_exists('isSalesPerson')) {
    function isSalesPerson()
    {
        $userType = getAuthData()->type ? getAuthData()->type : '';
        $salesPerson = $userType === 'sales_person' ?? false;
        return $salesPerson;
    };
}

if (!function_exists('mappedExhibitors')) {
    function mappedExhibitors($eventId)
    {
        $userId = getAuthData()->id ? getAuthData()->id : '';
        $exhibitorIds = Exhibitor::
            // where('event_id', $eventId)->
            where('sales_person_id', $userId)->pluck('id')->toArray();
        return $exhibitorIds ?? [];
    };
}

if (!function_exists('sendIntimateNotification')) {
    function sendIntimateNotification($data)
    {
        $currentEvent = getCurrentEvent();
        $receiverName = $data['name'] ?? '';
        $mobileNumber = $data['mobileNumber'] ?? '';
        // dd(empty($mobileNumber) || empty($receiverName), $receiverName, $mobileNumber,$data);

        if (empty($mobileNumber) || empty($receiverName)) {
            return ['status' => 'fail', 'message' => 'Please fill the required fields'];
        }

        $url = 'https://api.engati.com/whatsapp-api/v1.0/customer/68259/bot/0046ad9b644d4274/template';
        $templateName = '1to1_exhibitor_reminder';

        try {
            $payload = [
                "payload" => [
                    "name" => $templateName,
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $receiverName,
                                ],
                                [
                                    "type" => "text",
                                    "text" => $currentEvent->title,
                                ],
                            ],
                        ],
                    ],
                    "language" => [
                        "code" => "en_US",
                        "policy" => "deterministic",
                    ],
                    "namespace" => "dd96ee84_2909_4425_86ba_8a7fb56c2d2a",
                ],
                "phoneNumber" => $mobileNumber,
            ];

            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 0,
                    CURLOPT_POST => 1,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic a29ad3e1-2bb5-4126-9d44-153e96330a54-G4TG8QD",
                        "Content-Type: application/json",
                    ),
                )
            );

            $response = curl_exec($curl);

            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return ['status' => 'fail', 'message' => $err];
            } else {
                return ['status' => 'success', 'message' => 'Sent successfully', 'response' => $response];
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
        // $message = "Notification sent successfully";
        // return $message;
    }
}

if (!function_exists('getCurrentAnnouncement')) {
    function getCurrentAnnouncement()
    {
        $isVisitor = auth()->guard('visitor')->check();
        $visible_type = $isVisitor ? 'exhibitors_only' : 'visitors_only';
        $announcement = Announcement::where('visible_type', '!=', $visible_type)->where('is_active', 1)->orderBy('id', 'desc')->get();
        return $announcement ?? [];
    }
}

if (!function_exists('getAnnouncementId')) {
    function getAnnouncementId($announcementId)
    {
        $announcement = Announcement::where('id', $announcementId)->first();
        return $announcement ?? [];
    }
}


if (!function_exists('getUniqueUsernameFromGivenName')) {
    function getUniqueUsernameFromGivenName($givenName, $type = 'visitor')
    {
        $username = str_replace(' ', '-', $givenName);
        $username = strtolower($username);
        $username = $username . '-' . rand(10000, 99999999);
        while (Visitor::where('username', $username)->first()) {
            $username = $username . '-' . rand(10000, 99999999);
        }
        return $username;
    }
}


if (!function_exists('getEventByName')) {
    function getEventByName($eventName)
    {
        $event = Event::where('title', 'like', '%' . $eventName . '%')->first();
        return $event ?? null;
    }
}
