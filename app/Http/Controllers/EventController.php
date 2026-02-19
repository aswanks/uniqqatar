<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFeedback;
use App\Models\Eventregister;
use App\Models\EventFeedBackQuestion;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\EventsRegisteredDetailsExport;
use App\Exports\FeedbacklistsExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Str;
use Illuminate\Support\Facades\Http;

class EventController extends Controller
{
    # Function to Event Index - 16/06/2021
    public function eventControllerIndexEvent()
    {
        $events = Event::latest('id')
            ->select('id', 'tittle', 'contant', 'start_date', 'start_time','event_reg','priority','adult_status','child_above_10','child_below_5','child_5_to_10','end_date', 'end_time', 'image','evnt_pdf','certi', 'feedback_status','certi_status','status', 'created_at', 'updated_at', 'address')
            ->paginate(15);
        return view('backend/events/index', compact('events'));
    }
    
    # Function to Add Event - 16/06/2021
    public function eventControllerAddEvent()
    {
        return view('backend/events/add');
    }

    # Function to Store Event - 16/06/2021
    public function eventControllerStoreEvent()
    {
        $validator = Validator::make(request()->all(), [
            'tittle'      => 'required|string|max:255',
            'description' => 'required|string',
            'address'     => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'image'       => 'required|image|mimes:jpg,jpeg,png|max:2048',   
            'certi'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'evnt_pdf'    => 'nullable|file|mimes:pdf|max:5120',             
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $exists = Event::where('tittle', request()->tittle)
            ->where('start_date', request()->start_date)
            ->where('start_time', request()->start_time)
            ->where('end_date', request()->end_date)
            ->where('end_time', request()->end_time)
            ->where('address', request()->address)
            ->exists();

        if ($exists) {
            return Redirect::back()->with('error', 'This event already exists');
        }

        // Handle image upload
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $extension_img = $image->getClientOriginalExtension();
            $orginal_img_name = $image->getClientOriginalName();
            $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
            $image->move(base_path() . '/public/uploads/images/events', $imgName);
        } else {
            return Redirect::back()->with('error', 'Please Upload an Image');
        }

        // Handle PDF upload
        $pdfName = 0;
        if (request()->hasFile('evnt_pdf')) {
            $pdf = request()->file('evnt_pdf');
            $nevt_pdf = $pdf->getClientOriginalExtension();
            if ($nevt_pdf == 'pdf') {
                $orginal_pdf_name = $pdf->getClientOriginalName();
                $pdfName = setNewNameForPhoto($orginal_pdf_name) . '.' . generateRandomString(2, 15) . '.' . $nevt_pdf;
                $pdf->move(base_path() . '/public/uploads/images/events', $pdfName);
            } else {
                return Redirect::back()->with('error', 'Invalid PDF file.');
            }
        }

        // Handle certificate  upload
        if (request()->hasFile('certi')) {
            $certi = request()->file('certi');
            $extension_img = $certi->getClientOriginalExtension();
            $orginal_img_name = $certi->getClientOriginalName();
            $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
            $certi->move(base_path() . '/public/uploads/images/certificates', $imgName);
        }else {
            return Redirect::back()->with('error', 'Please Upload an Image');
        }

        // Save event details to the database
        $event = new Event;
        $event->tittle = request()->tittle;
        $event->contant = request()->description;
        $event->start_date = request()->start_date;
        $event->start_time = request()->start_time;
        $event->end_date = request()->end_date;
        $event->end_time = request()->end_time;
        $event->address = request()->address;
        $event->event_reg = request()->event_reg;
        $event->priority = request()->priority;
        $event->cert_description = request()->cert_description;
        $event->certi_status = (int)request()->certi_status;
        $event->image = $imgName;
        $event->evnt_pdf = $pdfName;
        $event->certi = $certiName;
        $event->status = 1;
        $event->save();
        
        $this->sendNotification($event);
        
        return Redirect()->route('events.index')->with('success', 'Event Added Successfully');
    }

    private function sendNotification()
    {
        $url = "https://uniq-rho.vercel.app/send-notification";

        // Fetch the latest offer
        $latestEvents = Event::latest()->first();

        if ($latestEvents) {
            // Prepare the offer data for the notification body
            $offerData = [
                'id' => $latestEvents->id,
                'title' => $latestEvents->tittle,  // Adjust fields based on your `Offer` model
                'description' => $latestEvents->contant,
            ];

            // Convert the offer data to a suitable format for the payload
            $payload = [
                "title" => "New Events Added",
                "body" => $latestEvents->tittle,
                "data" =>[
                    "key1"=> "events"
                ], // You can customize this field
            ];

            // Send the notification via HTTP POST request
            try {
                $response = Http::post($url, $payload);

                if ($response->successful()) {
                    return response()->json([
                        "message" => "Notification Sent",
                        "response" => $response->json(),
                    ]);
                } else {
                    return response()->json([
                        "message" => "Error Sending Notification",
                        "error" => $response->body(),
                    ], $response->status());
                }
            } catch (\Exception $e) {
                return response()->json([
                    "message" => "Error Sending Notification",
                    "error" => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                "message" => "No Offers Found",
            ], 404);
        }
    }

    # Function to Delete Event - 16/06/2021
    public function eventControllerDelete($id)
    {
        $event = Event::findOrFail($id);
        if ($event) {
            $path_event = 'public/uploads/images/events/' . $event->image;
            @unlink($path_event);
            Event::destroy($id);
            return Redirect::route('events.index')->with('success', 'Event Deleted Successfully');
        } else {
            return Redirect::route('events.index')->with('error', 'Invalid Reqeust');
        }
    }

    # Function to Edit Event - 16/06/2021
    public function eventControllerEdit($id)
    {
        $event = Event::findOrFail($id);
        if ($event) {
            return view('backend/events/edit', compact('event'));
        } else {
            return Redirect::route('events.index')->with('error', 'Invalid Request');
        }
    }

    # Function to Update Event - 16/06/2021
    public function eventControllerUpdate()
    {
        // Validate request data
        $validator = Validator::make(request()->all(), [
            'id'          => 'required|exists:events,id',
            'tittle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('events', 'tittle')->ignore(request()->id),
            ],
            'description' => 'required|string',
            'address'     => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',   
            'certi'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'evnt_pdf'    => 'nullable|file|mimes:pdf|max:5120',             

        ]);

        if ($validator->fails()) {
             return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $id = request()->id;
        
        $exists = Event::where('tittle', request()->tittle)
        ->where('start_date', request()->start_date)
        ->where('start_time', request()->start_time)
        ->where('end_date', request()->end_date)
        ->where('end_time', request()->end_time)
        ->where('address', request()->address)
        ->where('id', '!=', $id)
        ->exists();

        if ($exists) {
            return Redirect::back()->with('error', 'This event already exists');
        }

        $eventData = [
            'tittle'           => request()->tittle,
            'contant'          => request()->description,
            'address'          => request()->address,
            'start_date'       => request()->start_date,
            'start_time'       => request()->start_time,
            'end_date'         => request()->end_date,
            'end_time'         => request()->end_time,
            'event_reg'        => request()->event_reg ?? 0,
            'priority'         => request()->priority ?? 0,
            'cert_description' => request()->cert_description,
            'certi_status'     => (int) (request()->certi_status ?? 0),
        ];

        // Handle image upload
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $extension_img = $image->getClientOriginalExtension();
            $orginal_img_name = $image->getClientOriginalName();
            $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
            $image->move(base_path() . '/public/uploads/images/events', $imgName);
            $eventData['image'] = $imgName;
        }

        // Handle PDF upload
        if (request()->hasFile('evnt_pdf')) {
            $pdf = request()->file('evnt_pdf');
            $orginal_pdf_name = $pdf->getClientOriginalName();
            $pdfName = setNewNameForPhoto($orginal_pdf_name) . '.' . generateRandomString(2, 15) . '.' . $pdf->getClientOriginalExtension();
            $pdf->move(base_path() . '/public/uploads/images/events', $pdfName);
            $eventData['evnt_pdf'] = $pdfName;
        }

        // Handle certificate  upload
        if (request()->hasFile('certi')) {
            $certi = request()->file('certi');
            $extension_img = $certi->getClientOriginalExtension();
            $orginal_img_name = $certi->getClientOriginalName();
            $certiName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
            $certi->move(base_path() . '/public/uploads/images/certificates', $certiName);
            $eventData['certi'] = $certiName;
        }else {
            return Redirect::back()->with('error', 'Please Upload an Image');
        }

        // Update event data in the database
        Event::where('id', $id)->update($eventData);

        return Redirect::route('events.index')->with('success', 'Event Updated Successfully');
    }

    # Function to Search Event - 16/06/2021
    public function eventControllerSearch()
    {
        $key       = request()->get('search');
        $startDate = request()->get('start_date');
        $endDate   = request()->get('end_date');

        if (!$key && !$startDate && !$endDate) {
            return redirect()
                ->route('events.index')
                ->with('error', 'Please enter a Keyword or Date');
        }

        $events = Event::latest('id')
            ->where(function ($query) use ($key) {

                if ($key) {
                    $query->where('events.id', 'like', "%{$key}%")
                        ->orWhere('events.tittle', 'like', "%{$key}%")
                        ->orWhere('events.contant', 'like', "%{$key}%")
                        ->orWhere('events.start_time', 'like', "%{$key}%")
                        ->orWhere('events.end_time', 'like', "%{$key}%")
                        ->orWhere('events.address', 'like', "%{$key}%");
                }
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('events.start_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('events.end_date', '<=', $endDate);
            })
            ->paginate(15)
            ->appends(request()->query());

        return view('backend.events.index', compact('events'));
    }
    
    # Function to export EventFeedBack - 16/06/2021
    public function exporteventFeedBackPDF(Request $request)
    {
        $key = $request->get('searchquery');

        $eventName = 'All Events';

        if ($key) {
            $event = DB::table('events')
                ->where('tittle', 'LIKE', '%' . $key . '%')
                ->first();

            if ($event) {
                $eventName = $event->tittle;
            }
        }

        $feedbacks = DB::table('event_feedback as ef')
            ->join('users as u', 'u.id', '=', 'ef.user_id')
            ->join('events as e', 'e.id', '=', 'ef.event_id')
            ->select(
                'ef.id',
                'u.firstname',
                'e.tittle as event_name',
                'ef.role',
                'ef.answer',
                'ef.suggestion',
                'ef.created_at'
            )
            ->when($key, function ($query) use ($key) {
                $query->where('e.tittle', 'LIKE', '%' . $key . '%');
            })
            ->orderBy('ef.id', 'DESC')
            ->get();

        $questions = DB::table('event_feedback_questions')
            ->select('id', 'question')
            ->get()
            ->keyBy('id');

        $pdf = PDF::loadView(
            'backend.events.event_feedback_list_pdf',
            compact('eventName', 'feedbacks', 'questions')
        );

        $date = date('d-m-Y');
        $fileName = $eventName . '_Feedback_' . $date . '.pdf';

        return $pdf->download($fileName);
    }

    # Function to Search Event FeedBack - 28/12/2025
    public function eventFeedBackControllerSearch()
    {
        $key = request('key');

        if (empty($key)) {
            return redirect()
                ->route('eventfeedbacks')
                ->with('error', 'Please enter a keyword for searching');
        }

        $eventFeedbacklists = EventFeedback::select('event_feedback.*')
            ->join('events', 'events.id', '=', 'event_feedback.event_id')
            ->join('users', 'users.id', '=', 'event_feedback.user_id')
            ->where(function ($query) use ($key) {
                $query->where('events.tittle', 'like', "%{$key}%")
                    ->orWhere('event_feedback.role', 'like', "%{$key}%")
                    ->orWhere('users.firstname', 'like', "%{$key}%");
            })
            ->orderByDesc('event_feedback.id')
            ->paginate(15)
            ->appends(['key' => $key]);

        return view(
            'backend.events.feedback-list',
            compact('eventFeedbacklists', 'key')
        );
    }

    # Function to Search Event Registration - 28/12/2025
    public function eventregisterform(Request $request)
    {
        $key = request()->get('search');
        $query = DB::table('registrations as r')
                ->join('eventregisters as er', 'r.user_id', '=', 'er.user_id')
                ->join('events as e', 'er.event_id', '=', 'e.id')
                ->select(
                    'r.first_name',
                    'r.last_name',
                    'e.tittle',
                    'r.email',
                    'r.mob_no',
                    'r.qid',
                    'er.id',
                    'er.no_child_below_5',
                    'er.no_child_5_to_10',
                    'er.number_adult',
                    'er.workplace'
                )
                ->when($key, function ($query, $key) {
                    return $query->where('e.tittle', 'like', '%' . $key . '%');
                })
                ->latest('r.id')
                ->get();

            return view('backend.eventregister.index', compact('query', 'key'));

    }
    
    # Function to Event Registration Delete - 28/12/2025
    public function eventregisterdelete($id)
    {
        DB::table('eventregisters')->where('id', $id)->delete();

        return redirect()
            ->route('events.register.index')
            ->with('success', 'Event registration deleted successfully');
    }
    
    # Function to updateStatus - 28/12/2025
    public function updateStatus(Request $request)
    {

        $data = [];
        
        if (request()->type === 'adult') {
            $data['adult_status'] = request()->status;
        }

        if (request()->type === 'child_above_10') {
            $data['child_above_10'] = request()->status;
        }
        
        if (request()->type === 'child_below_5') {
            $data['child_below_5'] = request()->status;
        }

        if (request()->type === 'child_5_to_10') {
            $data['child_5_to_10'] = request()->status;
        }
        
        if (request()->type === 'feedback_status') {
            $data['feedback_status'] = request()->status;
        }

         if (empty($data)) {
        return response()->json([
            'status' => 400,
            'message' => 'Invalid status type'
        ], 400);
     }  

    $updated = Event::where('id', $request->id)->update($data);

    if (!$updated) {
        return response()->json([
            'status' => 404,
            'message' => 'Record not found'
        ], 404);
    }

    return response()->json([
        'status' => 200,
        'message' => 'Status updated successfully'
    ], 200);
        
    
    }

    # Function to Export PDF - 28/12/2025
    public function exportPDF(Request $request)
    {
        $key = request()->get('searchquery');
        
        $query = DB::table('registrations as r')
            ->join('eventregisters as er', 'r.user_id', '=', 'er.user_id')
            ->join('events as e', 'er.event_id', '=', 'e.id')
            ->select(
                'r.first_name',
                'r.last_name',
                'e.tittle as event_name', // Ensure this is the correct column name
                'r.email',
                'r.mob_no',
                'r.qid',
                'er.no_child_below_5',
                'er.no_child_5_to_10', // Use the correct column name here
                'er.number_adult',  // Use the correct column name here
                'er.workplace'
            )
            ->when($key, function ($query, $key) {
                return $query->where('e.tittle', 'like', '%' . $key . '%');
            })
            ->latest('r.id')
            ->get();

        $pdf = PDF::loadView('backend.eventregister.registration', compact('query'));
        return $pdf->download('registrations.pdf');

    }
    
    # Function to Export Excel - 28/12/2025
    public function exportExcel(Request $request)
    {
        $key = $request->get('searchquery');

        if (empty($key)) {
            return Excel::download(
                new EventsRegisteredDetailsExport(null),
                'All_Events_Registrations_' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        $eventName = DB::table('events')
            ->where('tittle', 'like', '%' . $key . '%')
            ->value('tittle');

        $fileEventName = $eventName
            ? Str::slug($eventName, '_')
            : 'Filtered_Events';

        return Excel::download(
            new EventsRegisteredDetailsExport($key),
            $fileEventName . '_Registrations_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    # Function to Export EventBackExcel - 28/12/2025
    public function exporteventFeedBackExcel(Request $request)
    {
        $key = $request->get('searchquery');

        if (empty($key)) {
            return Excel::download(
                new FeedbacklistsExport(null),
                'All_Feedback_lists_' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        // Get event title for filename (from events table)
        $eventName = DB::table('event_feedback')
            ->join('events', 'events.id', '=', 'event_feedback.event_id')
            ->join('users', 'users.id', '=', 'event_feedback.user_id')
            ->where(function ($query) use ($key) {
                $query->where('events.tittle', 'like', "%{$key}%")
                    ->orWhere('users.first_name', 'like', "%{$key}%");
            })
            ->value('events.tittle');

        $fileEventName = $eventName
            ? Str::slug($eventName, '_')
            : 'Filtered_Feedback';

        return Excel::download(
            new FeedbacklistsExport($key),
            $fileEventName . '_Feedback_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
        
    # Function to Export FeedBack List - 28/12/2025
    public function eventFeedbacklist()
    {
       $key = request()->get('searchquery');
       $eventFeedbacklists = EventFeedback::orderBy('id', 'desc')->paginate(15);
       return view('backend/events/feedback-list', compact('eventFeedbacklists','key')); 
    }

    # Function to EventFeedBackQuestion Create - 28/12/2025
    public function eventFeedbackQutCreate()
    {
         $events = Event::where('status',1)->orderBy('id','DESC')->get();
         return view('backend/event-feedbacks/create',compact('events')); 
    }

    # Function to EventFeedBackQuestion Lsit - 28/12/2025
    public function eventFeedbackQutlist()
    {
        $eventFeedbackQutlists = EventFeedBackQuestion::orderBy('id', 'desc')->paginate(15);
        return view('backend/event-feedbacks/index', compact('eventFeedbackQutlists')); 
    }
    
    # Function to EventFeedBackQuestion Store - 28/12/2025
    public function eventFeedbackQutStore(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'event_id' => 'required',
            'type' => 'required',
            'question' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        $eventFeedbackQuest = new EventFeedBackQuestion ;
        $eventFeedbackQuest->event_id = request()->event_id;
        $eventFeedbackQuest->type = request()->type;
        $eventFeedbackQuest->question = request()->question;
        $eventFeedbackQuest->is_required = request()->is_required;
        $eventFeedbackQuest->save();
        return redirect()
            ->route('eventfeedback.questions.list')
            ->with('success', 'Event feedback question created successfully!');

    }
    
    # Function to EventFeedBackQuestion Update - 28/12/2025
    public function eventFeedbackQutUpdate(Request $request,$id)
    {
        $validator = Validator::make(request()->all(), [
            'event_id' => 'required|exists:events,id',
            'type' => 'required',
            'question' => 'required|string|max:1000'
        ]);
        
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $eventFeedbackQuest = EventFeedBackQuestion::find($id);
        if (!$eventFeedbackQuest) {
            return redirect()
                ->route('eventfeedback.questions.list')
                ->with('error', 'Feedback question not found.');
        }
        $eventFeedbackQuest->event_id = $request->event_id;
        $eventFeedbackQuest->type = $request->type;
        $eventFeedbackQuest->question = $request->question;
        $eventFeedbackQuest->is_required = (int)$request->is_required;
        
        if($eventFeedbackQuest->save()){
             return redirect()
            ->route('eventfeedback.questions.list')
            ->with('success', 'Event feedback question updated  successfully!');
        }else{
            return redirect()
            ->route('eventfeedback.questions.list')
            ->with('error', 'Failed to update feedback question.');
        }
       

    }

    # Function to EventFeedBackQuestion Edit - 28/12/2025
    public function eventFeedbackQutEdit($id)
    {
        $eventQut = EventFeedBackQuestion::find($id);
        $events = Event::where('status',1)->orderBy('id','DESC')->get();
        return view('backend/event-feedbacks/edit',compact('events','eventQut'));
    }

    # Function to EventFeedBackQuestion Delete - 28/12/2025
    public function eventFeedbackQutDelete($id)
    {
        $eventQut = EventFeedBackQuestion::find($id);
        if ($eventQut) {

            EventFeedBackQuestion::destroy($id);
            return Redirect::route('eventfeedback.questions.list')->with('success', 'Event Feedback Question Deleted Successfully !');
        } else {
            return Redirect::route('eventfeedback.questions.list')->with('error', 'Invalid Reqeust !');
        }
    }
}