<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmailLink;
use App\Models\Attempt;
use App\Models\Publication;
use App\Models\Review;
use App\Models\Package;
use App\Models\Section;
use App\Models\Session;
use App\Models\Option;
use App\Models\Question;
use App\Models\Answer;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $users = User::all();
        return view('admin.index')->with('users',$users);

    }

    public function gotochangepassword()
    {
        return view('auth.change-password');
    }

    public function adduser()
    {
        return view('admin.add-user');
    }

    public function addUserViaMail($name,$email,$role,$number)
    {
        return view('auth.register-by-invite')->with([
            'name'=>$name,
            'email'=>$email,
            'role'=>$role,
            'number'=>$number
        ]);;
    }
   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            $roles = Role::all();
            return view('admin.edit')->with([
                'user'=>$user,
                'roles'=>$roles
            ]);
        }
        else
        {
            if($user->hasRole('student'))
            {
                $roles = Role::all();
                return view('admin.edit')->with([
                    'user'=>$user,
                    'roles'=>$roles
                ]);
            }
            else{
                return redirect()->route('index');

            }
        }
    }

    public function export_users_information(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            $roles = Role::all();
            $pdf = PDF::loadView('pdf', [
                'user'=>$user,
                'roles'=>$roles
            ])->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParseEnabled' => true , 'isRemoteEnabled' => true]);
            return $pdf->download($user->userid.'.pdf');
            
        }
        else
        {
            return Redirect()->back()->with('message', 'You are not Allowed for this Action');   
        }
        
    }

    public function userDetails(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            $roles = Role::all();
            return view('admin.user_detail')->with([
                'user'=>$user,
                'roles'=>$roles
            ]);
        }
        else
        {
            if($user->hasRole('student'))
            {
                $roles = Role::all();
                return view('admin.user_detail')->with([
                    'user'=>$user,
                    'roles'=>$roles
                ]);
            }
            else{
                return redirect()->route('index');

            }
        }
    }

    public function profile()
    {
        $roles = Role::all();
        $user = Auth::user();
        return view('profile')->with([
            'user'=>$user,
            'roles'=>$roles
        ]);
    }
    public function libraries()
    {
        $publications = Publication::where('approved',1)->get();
        $first = Publication::where('approved',1)->first();
        return view('libraries')->with([
            'publications'=>$publications,
            'first'=>$first,
        ]);
    }
    public function librariesapproval()
    {
        $sno=0;
        $publications = Publication::where('approved',0)
        ->join('users','users.id','=','publications.userid')
        ->select('publications.id as p_id','users.name','users.email','users.profilepic','publications.title','publications.content','publications.created_at')
        ->get();
        // dd($publications);
        return view('admin.libraries_approval')->with([
            'publications'=>$publications,
            'sno'=>$sno
        ]);
    }
    public function publication($id)
    {
        $publication = Publication::where('id',$id)->first();
        return view('admin.view-publication')->with('publication',$publication);
    }
    public function editpublication($id)
    {
        $publication = Publication::where('id',$id)->first();
        return view('admin.edit-publication')->with('publication',$publication);
    }
    public function approvepublication($id)
    {
        $publication = Publication::where('id',$id)->first();
        $user_id = Publication::where('id',$id)->pluck('userid');
        $user = User::where('id',$user_id)->first();
        if($user->hasRole('student'))
        {
            $user->wallet+=1;
        }
        else if($user->hasRole('contributor'))
        {
            $user->wallet+=2;
        }
        $user->save();
        $publication->approved = 1;
        $publication->save();
        return redirect()->route('libraries-approval')->with('message', 'Publication Approved Succecfully');
    }
    public function viewpublication($id)
    {
        $publications = Publication::all()->where('approved',1);
        $first = Publication::where('id',$id)->first();
        return view('libraries')->with([
            'publications'=>$publications,
            'first'=>$first
        ]);
    }
    public function newpublication()
    {
        return view('new-publication');
    }
    public function storepublication(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:100000'],
        ]);
        $publication = new Publication();
        $publication->userid = Auth::user()->id;
        $publication->title = $request->title;
        if(Auth::user()->hasRole('admin'))
        {
            $publication->approved = 1;
            $publication->content = $request->content;
            $publication->save();
            return redirect()->route('libraries')->with('message', 'Publication Added Successfully');
        }
        else
        {
            $publication->approved = 0;
            $publication->content = $request->content;
            $publication->save();
            return redirect()->route('libraries')->with('message', 'Publication Approval Request has been sent to Admin');
        }
        $publication->content = $request->content;
        $publication->save();
        return redirect()->route('libraries')->with('message', 'Publication Approval Request has been sent to Admin');
    }
    public function updatepublication(Request $request, Publication $publication)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:100000'],
        ]);
        $publication->title = $request->title;
        $publication->content = $request->content;
        $publication->save();
        return redirect()->route('libraries')->with('message', 'Publication Updated SuccessFully');
    }
    public function deletepublication($id)
    {
        $publication = Publication::where('id',$id)->first();
        $publication->delete();
        return redirect()->route('libraries')->with('message', 'Publication Deleted Succecfully');
    }

    public function packages()
    {
        $packages = Package::all();
        $first = Package::first();
        return view('packages')->with([
            'packages'=>$packages,
            'first'=>$first,
        ]);
    }
    public function viewpackage($id)
    {
        $packages = Package::all();
        $first = Package::where('id',$id)->first();
        return view('packages')->with([
            'packages'=>$packages,
            'first'=>$first
        ]);
    }
    public function editpackage($id)
    {
        $package = Package::where('id',$id)->first();
        return view('admin.edit-package')->with('package',$package);
    }
    public function newpackage()
    {
        return view('admin.new-package');
    }
    public function storepackage(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:100000'],
        ]);
        $package = new Package();
        $package->title = $request->title;
        $package->amount = $request->amount;
        $package->content = $request->content;
        $package->save();
        return redirect()->route('packages')->with('message', 'Package Added Successfully');
    }
    public function updatepackage(Request $request, Package $package)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:100000'],
        ]);
        $package->title = $request->title;
        $package->amount = $request->amount;
        $package->content = $request->content;
        $package->save();
        return redirect()->route('packages')->with('message', 'Package Updated SuccessFully');
    }
    public function deletepackage($id)
    {
        $package = Package::where('id',$id)->first();
        $package->delete();
        return redirect()->route('packages')->with('message', 'Package Deleted Succecfully');
    }

    public function sessions()
    {
        $sections = Session::join('sections','sections.id','=','sessions.sectionid')->get();
        $first = Section::first();
        $go = Section::count();
        $Exam1 = Attempt::where('sectionid',$first->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',1)
        ->first();
        $Exam2 = Attempt::where('sectionid',$first->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',2)
        ->first();
        $Exam3 = Attempt::where('sectionid',$first->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',3)
        ->first();
        $Exam4 = Attempt::where('sectionid',$first->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',4)
        ->first();
        if($first)
        {
            $session = Session::where('sectionid',$first->id)->first();
            return view('sessions')->with([
                'sections'=>$sections,
                'session'=>$session,
                'first'=>$first,
                'Exam1'=>$Exam1,
                'Exam2'=>$Exam2,
                'Exam3'=>$Exam3,
                'Exam4'=>$Exam4,
                'go'=>$go,
            ]);
        }
        else
        {
            $session = NULL;
            return view('sessions')->with([
                'sections'=>$sections,
                'session'=>$session,
                'go'=>$go,
            ]);
        }
    }
    public function viewsessions($id)
    {
        $sections = Session::join('sections','sections.id','=','sessions.sectionid')->get();
        $first = Section::where('id',$id)->first();
        $session = Session::where('sectionid',$id)->first();
        $go = Section::count();
        $Exam1 = Attempt::where('sectionid',$first->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',1)
        ->first();
        $Exam2 = Attempt::where('sectionid',$first->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',2)
        ->first();
        $Exam3 = Attempt::where('sectionid',$first->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',3)
        ->first();
        $Exam4 = Attempt::where('sectionid',$first->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',4)
        ->first();

        return view('sessions')->with([
            'sections'=>$sections,
            'session'=>$session,
            'first'=>$first,
            'Exam1'=>$Exam1,
            'Exam2'=>$Exam2,
            'Exam3'=>$Exam3,
            'Exam4'=>$Exam4,
            'go'=>$go,

        ]);
    }
    public function newsession()
    {
        $ids = Session::pluck('sectionid');
        // dd();
        $sections = Section::whereNotIn('id', $ids)->get();
        return view('admin.new-session')->with('sections',$sections);
    }
    public function storesession(Request $request)
    {
        $request->validate([
            'numberOfQuestions' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'string', 'max:255'],
        ]);
        
        $avlquestions = Question::join('options','options.id','=','questions.optionid')->where('options.sectionid',$request->section)->count();
        if($avlquestions<($request->numberOfQuestions*4))
        {
            $message = "Insufficient Questions in this Section";
            return redirect()->back()->with('messages',$message);
        }
        else
        {
            $session = new Session();
            $session->numberOfQuestions = $request->numberOfQuestions;
            $session->sectionid = $request->section;
            $session->duration = $request->duration;
            $session->save();
            return redirect()->route('sessions')->with('message', 'Session Added Successfully');
        }
    }
    public function editsession($id)
    {
        $session = Session::where('id',$id)->first();
        $ids = Session::pluck('sectionid');
        $sections = Section::whereNotIn('id', $ids)->get();
        $sectiontitle = Section::where('id',$session->sectionid)->value('title');
        // dd($sectiontitle);
        return view('admin.edit-session')->with([
            'session'=>$session,
            'sectiontitle'=>$sectiontitle,
            'sections'=>$sections
        ]);
    }
    public function updatesession(Request $request, Session $session)
    {
        $request->validate([
            'numberOfQuestions' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'string', 'max:255'],
        ]);
        $avlquestions = Question::join('options','options.id','=','questions.optionid')->where('options.sectionid',$request->section)->count();
        if($avlquestions<($request->numberOfQuestions*4))
        {
            $message = "Insufficient Questions in this Section";
            return redirect()->back()->with('messages',$message);
        }
        else
        {
            $session->numberOfQuestions = $request->numberOfQuestions;
            $session->sectionid = $request->section;
            $session->duration = $request->duration;
            $session->save();
            return redirect()->route('sessions')->with('message', 'Session Updated SuccessFully');
        }
    }
    public function deletesession($id)
    {
        $session = session::where('id',$id)->first();
        $session->delete();
        return redirect()->route('sessions')->with('message', 'Session Deleted Succecfully');
    }
    public function attempt($exam,$id)
    {   
        $duration = Session::where('id',$id)->value('duration');
        $Sectionid = Session::where('id',$id)->pluck('sectionid');
        $numberOfQuestion = Session::where('id',$id)->first();
        $section = Section::where('id',$Sectionid)->first();
        $no = $numberOfQuestion->numberOfQuestions;
        $sno = 0;

        $alreadyattempted = Attempt::where('sectionid',$section->id)
        ->where('userid',Auth::user()->id)
        ->where('exam',$exam)
        ->first();
        $lastattempted = Attempt::where('sectionid',$section->id)
        ->where('userid',Auth::user()->id)
        // ->value('exam')
        ->orderBy('created_at', 'desc')->first();
        // dd($lastattempted);
        if($alreadyattempted)
        {
            return redirect()->back()->with('message', 'Already Attempted');
        }
        else 
        {
            if($lastattempted)
            {
                if($exam-$lastattempted->exam==1)
                {
                    $oldquestions = Answer::join('attempts','attempts.id','=','answers.attemptid')
                    ->where('sectionid',$section->id)
                    ->where('userid',Auth::user()->id)
                    ->get('answers.question');
                    $questions = Question::join('options','options.id','=','questions.optionid')->where('options.sectionid',$Sectionid)->whereNotIn('questions.question', $oldquestions)->inRandomOrder()->limit($no)->get();
                    $attempt = new Attempt();
                    $attempt->sectionid = $section->id;
                    $attempt->session = $section->title;
                    $attempt->userid = Auth::user()->id;
                    $attempt->exam = $exam;
                    $attempt->save();
                    
                    return view('attempt')->with([
                        'questions'=>$questions,
                        'section'=>$section,
                        'exam'=>$exam,
                        'attempt'=>$attempt,
                        'duration'=>$duration,
                        'sno'=>$sno
                    ]);
                }
                else
                {
                    return redirect()->back()->with('message', 'Attempt the Remaining Exams First');
                }
            }
            else
            {
                if($exam==1)
                {
                    $questions = Question::join('options','options.id','=','questions.optionid')->where('options.sectionid',$Sectionid)->inRandomOrder()->limit($no)->get();
                    $attempt = new Attempt();
                    $attempt->sectionid = $section->id;
                    $attempt->userid = Auth::user()->id;
                    $attempt->session = $section->title;
                    $attempt->exam = $exam;
                    $attempt->save();
                    
                    return view('attempt')->with([
                        'questions'=>$questions,
                        'section'=>$section,
                        'exam'=>$exam,
                        'attempt'=>$attempt,
                        'duration'=>$duration,
                        'sno'=>$sno
                    ]);
                }
                else
                {
                    return redirect()->back()->with('message', 'Attempt the Remaining Exams First');
                }
            }
        }
        
    }
    public function storeattempt(Request $request,$exam,$id,$attemptid)
    {
        $a = 0;
        foreach($request->question as $question)
        {
            $answer= new Answer();
            $answer->attemptid = $attemptid;
            $answer->question = $question;
            $answer->answer = $request->answer[$a];
            $answer->save();
            $a++;
        }
        // dd($a);
        return redirect()->route('sessions')->with('message', 'Exam Attempt has been Submitted');
    }
    public function sessionattempts()
    {
        $sno=0;
        $attempts = Attempt::join('users','users.id','=','attempts.userid')
        ->select('attempts.id as a_id','users.name','users.email','users.profilepic','attempts.session','attempts.exam','attempts.status','attempts.created_at','attempts.score')
        ->get();
        // dd($publications);
        return view('admin.session-attempts')->with([
            'attempts'=>$attempts,
            'sno'=>$sno
        ]);
    }
    public function activitylog()
    {
        $sno=0;
        $attempts = Attempt::join('users','users.id','=','attempts.userid')->where('users.id',Auth::user()->id)
        ->select('attempts.id as a_id','users.name','users.email','users.profilepic','attempts.session','attempts.exam','attempts.status','attempts.created_at','attempts.score')
        ->get();
        // dd($publications);
        return view('student.activity-log')->with([
            'attempts'=>$attempts,
            'sno'=>$sno
        ]);
    }
    public function viewattempt($id)
    {
        $answers = Answer::where('attemptid',$id)->get();
        $attempt = Attempt::where('id',$id)->first();
        return view('admin.view-attempt')->with([
            'answers'=>$answers,
            'attempt'=>$attempt,
            'id'=>$id
        ]);
    }
    
    public function storeattemptstatus(Request $request,$id)
    {
        $attempt = Attempt::where('id',$id)->first();
        $attempt->score = $request->score;
        $attempt->status = $request->status;
        $attempt->save();
        return redirect()->route('session-attempts')->with('message', 'Score Assigned');

    }


    public function sections()
    {
        $sections = Section::all();
        $first = Section::first();
        $go = Section::count();
        
        if($first)
        {
            $options = Option::where('sectionid',$first->id)->get();
            return view('sections')->with([
                'sections'=>$sections,
                'options'=>$options,
                'first'=>$first,
                'go'=>$go,
            ]);
        }
        else
        {
            $options = NULL;
            return view('sections')->with([
                'sections'=>$sections,
                'options'=>$options,
                'go'=>$go,
            ]);
        }
    }
    public function viewoptions($id)
    {
        $sections = Section::all();
        $first = Section::where('id',$id)->first();
        $options = Option::where('sectionid',$id)->get();
        $go = Section::count();
        
        return view('sections')->with([
            'sections'=>$sections,
            'options'=>$options,
            'first'=>$first,
            'go'=>$go,

        ]);
    }
    public function editsection($id)
    {
        $section = Section::where('id',$id)->first();
        return view('admin.edit-section')->with('section',$section);
    }
    public function newsection()
    {
        return view('admin.new-section');
    }
    public function storesection(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $section = new Section();
        $section->title = $request->name;
        $section->save();
        return redirect()->route('sections')->with('message', 'Section Added Successfully');
    }
    public function updatesection(Request $request, Section $section)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $section->title = $request->name;
        $section->save();
        return redirect()->route('sections')->with('message', 'Section Updated SuccessFully');
    }
    public function deletesection($id)
    {
        $section = Section::where('id',$id)->first();
        $section->delete();
        return redirect()->route('sections')->with('message', 'Section Deleted Succecfully');
    }

    public function editoption($id)
    {
        $option = Option::where('id',$id)->first();
        return view('admin.edit-option')->with('option',$option);
    }
    public function newoption()
    {
        $sections = Section::all();
        return view('admin.new-option')->with('sections',$sections);
    }
    public function storeoption(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
        ]);
        $option = new Option();
        $option->title = $request->name;
        $option->sectionid = $request->section;
        $option->save();
        return redirect()->route('sections')->with('message', 'Course Added Successfully');
    }
    public function updateoption(Request $request, Option $option)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $option->title = $request->name;
        $option->save();
        return redirect()->route('sections')->with('message', 'Course Updated SuccessFully');
    }
    public function deleteoption($id)
    {
        $option = Option::where('id',$id)->first();
        $option->delete();
        return redirect()->route('sections')->with('message', 'Course Deleted Succecfully');
    }

    public function questions()
    {
        $options = Option::all();
        $first = Option::first();
        $go = Option::count();
        
        if($first)
        {
            $questions = Question::where('optionid',$first->id)->where('enabled',1)->get();
            return view('questions')->with([
                'questions'=>$questions,
                'options'=>$options,
                'first'=>$first,
                'go'=>$go,
            ]);
        }
        else
        {
            $options = NULL;
            return view('questions')->with([
                'first'=>$first,
                'options'=>$options,
                'go'=>$go,
            ]);
        }
    }
    public function viewquestions($id)
    {
        $options = Option::all();
        $first = Option::where('id',$id)->first();
        $questions = Question::where('optionid',$id)->where('enabled',1)->get();
        $go = Option::count();
        
        return view('questions')->with([
            'questions'=>$questions,
            'options'=>$options,
            'first'=>$first,
            'go'=>$go,

        ]);
    }
    public function newquestion()
    {
        $options = Option::all();
        if(Auth::user()->hasRole('admin'))
        {
            if(Option::count()==0)
            {
                return redirect()->route('questions')->with('message', 'Add a Course First');
            }
            return view('new-question')->with('options',$options);
        }
        else
        {
            return view('new-question');
        }

    }
    public function storequestion(Request $request)
    {
        if(Auth::user()->hasRole('admin'))
        {
            $request->validate([
                'question' => ['required', 'string', 'max:255'],
                'answer' => ['required', 'string', 'max:255'],
            ]);
        }
        else
        {
            $request->validate([
                'question' => ['required', 'string', 'max:255'],
                'answer' => ['required', 'string', 'max:255'],
            ]);
        }
        $question = new Question();
        $question->enabled = 1;
        $question->question = $request->question;
        $question->userid = Auth::user()->id;
        $question->answer = $request->answer;
        if(Auth::user()->hasRole('admin'))
        {
            $option = Option::where('id',$request->option)->first();
            $question->option = $option->title;
            $question->optionid = $request->option;
            $question->approved = 1;
            $question->save();
            return redirect()->route('questions')->with('message', 'Question Added Successfully');
        }
        else
        {
            $question->approved = 0;
            $question->save();
            return redirect()->route('questions')->with('message', 'Question Approval Request has been sent to Admin');
        }
        
    }
    public function editquestion($id)
    {
        $question = Question::where('id',$id)->first();
        return view('admin.edit-question')->with('question',$question);
    }
    public function updatequestion(Request $request, Question $question)
    {
        $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:255'],
        ]);
        $question->question = $request->question;
        $question->answer = $request->answer;
        $question->save();
        return redirect()->route('questions')->with('message', 'Question Updated SuccessFully');
    }
    public function deletequestion($id)
    {   
        $question = Question::where('id',$id)->first();
        $question->delete();
        return redirect()->route('questions')->with('message', 'Question Deleted Succecfully');
    }
    public function questionsapproval()
    {
        $sno=0;
        $questions = Question::join('users','users.id','=','questions.userid')
        ->select('questions.id as p_id','users.name','users.email','users.profilepic','questions.question','questions.answer','questions.option','questions.created_at','questions.enabled','questions.approved')
        ->get();

        // dd($publications);
        return view('admin.questions_approval')->with([
            'questions'=>$questions,
            'sno'=>$sno
        ]);
    }
    public function question($id)
    {
        $question = Question::where('id',$id)->first();
        return view('admin.view-question')->with('question',$question);
    }
    public function assignquestion($id)
    {
        $options = Option::all();
        $question = Question::where('id',$id)->first();
        return view('admin.assign-question')->with([
            'options'=>$options,
            'question'=>$question,
        ]);
    }
    public function storeassignquestion(Request $request)
    {
        $question = Question::where('id',$request->id)->first();
        $option = Option::where('id',$request->option)->first();
        $question->option = $option->title;
        $question->optionid = $request->option;
        $question->save();
        return redirect()->route('questions-approval')->with('message', 'Question Assigned to Course Succecfully');
    }
    public function questionenabled(Request $request)
    {
        $question = Question::where('id',$request->id)->first();
        // dd($question);

        if($question->enabled == 1)
        {
            $question->enabled = 0;
            $question->save();
            return redirect()->route('questions-approval')->with('message', 'Question Disabled Succecfully');
        }
        else if($question->enabled == 0)
        {
            $question->enabled = 1;
            $question->save();
            return redirect()->route('questions-approval')->with('message', 'Question Enabled Succecfully');
        }
        
    }
    
    public function approvequestion($id)
    {
        $question = Question::where('id',$id)->first();
        if($question->optionid!=NULL)
        {
            $user_id = Question::where('id',$id)->pluck('userid');
            $user = User::where('id',$user_id)->first();
            if($user->hasRole('student'))
            {
                $user->wallet+=1;
            }
            else if($user->hasRole('contributor'))
            {
                $user->wallet+=2;
            }
            $user->save();
            $question->approved = 1;
            $question->save();
            return redirect()->route('questions-approval')->with('message', 'Question Approved Succecfully');
        }
        else if($question->optionid==NULL)
        {
            return redirect()->route('questions-approval')->with('issue', 'Assign Question to any Course First');
        }
    }

    public function reviews()
    {
        $reviews = Review::where('approved',1)->get();
        $first = Review::where('approved',1)->first();
        return view('reviews')->with([
            'reviews'=>$reviews,
            'first'=>$first,
        ]);
    }
    public function reviewsapproval()
    {
        $sno=0;
        $reviews = Review::where('approved',0)
        ->join('users','users.id','=','reviews.userid')
        ->select('reviews.id as p_id','users.name','users.email','users.profilepic','reviews.content','reviews.created_at')
        ->get();
        // dd($publications);
        return view('admin.reviews_approval')->with([
            'reviews'=>$reviews,
            'sno'=>$sno
        ]);
    }
    public function review($id)
    {
        $review = Review::where('id',$id)->first();
        return view('admin.view-reviews')->with('review',$review);
    }
    
    public function approvereviews($id)
    {
        $review = Review::where('id',$id)->first();
        $user_id = Review::where('id',$id)->pluck('userid');
        $user = User::where('id',$user_id)->first();
        if($user->hasRole('student'))
        {
            $user->wallet+=1;
        }
        else if($user->hasRole('contributor'))
        {
            $user->wallet+=2;
        }
        $user->save();
        $review->approved = 1;
        $review->save();
        return redirect()->route('reviews-approval')->with('message', 'Review Approved Succecfully');
    }
    public function viewreviews($id)
    {
        $reviews = Review::all()->where('approved',1);
        $first = Review::where('id',$id)->first();
        return view('reviews')->with([
            'reviews'=>$reviews,
            'first'=>$first
        ]);
    }
    public function newreviews()
    {
        return view('new-reviews');
    }
    public function storereviews(Request $request)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:100000'],
        ]);
        $review = new Review();
        $review->userid = Auth::user()->id;
        $review->name = Auth::user()->name;
        $review->approved = 0;
        $review->content = $request->content;
        $review->save();
        return redirect()->route('reviews')->with('message', 'Review Approval Request has been sent to Admin');
    }
    
    public function deletereviews($id)
    {
        $review = Review::where('id',$id)->first();
        $review->delete();
        return redirect()->route('reviews')->with('message', 'Review Deleted Succecfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
            'email' => ['required', 'string', 'email', 'max:255']
        ]);
        if($request->profilepic != null)
        {
        $profilepic = app('App\Http\Controllers\UploadImageController')->storage_upload($request->profilepic,'/app/public/Users/Profile/');
            $user->profilepic = $profilepic;

        }
        $user->roles()->sync($request->roles);
        $user->name = $request->name;
        $user->number = $request->number;
        $user->save();
        
        return Redirect()->back()->with('message', 'Details Updated Successfully');   
    }

    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        // $user->userid = Auth::user()->userid;
        // $user->six_digit_Id = Auth::user()->six_digit_Id;
        // $user->name = Auth::user()->name;
        // $user->email = Auth::user()->email;
        // $user->lock = Auth::user()->lock;
        // $user->number = Auth::user()->number;
        
        DB::table('users')
        ->where('id', Auth::user()->id)  // find your user by their email
        ->update(array('password' => Hash::make($request->password)));
        
        return redirect()->route('index');
    }


    public function lockuser(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            if($user->lock==1)
            {
                $user->lock = 0;
                $user->save();
                return redirect()->route('index');
            }
            else
            {
                $user->lock = 1;
                $user->save();
                return redirect()->route('index');
            }
        }
        else
        {
            if($user->hasRole('student'))
            {
                if($user->lock==1)
                {
                    $user->lock = 0;
                    $user->save();
                    return redirect()->route('index');
                }
                else
                {
                    $user->lock = 1;
                    $user->save();
                    return redirect()->route('index');
                }
            }
            else{
                return redirect()->route('index');

            }
        }
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
        $name = $request->name; 
        $email = $request->email;
        $role = $request->role;
        $number = $request->number;
  
        Mail::to($email)->send(new SendEmailLink($name,$email,$role,$number));
        return Redirect()->back()->with('message', 'Invitation Sent Successfully');   
    }

    public function storeuser(Request $request)
    {
        // dd($request->role);

        $date = date('dmy');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profilepic' => ['required', 'max:10000'],
        ]);
        $last_id = User::orderBy('six_digit_Id', 'desc')->first()->six_digit_Id;
        $six_digit = ++$last_id;
        $six_digit_Id = substr($six_digit,1);
        if($request->role == 'student')
        {
            $userid = 'STUD-'.$date. '-'.$six_digit_Id;
        }
        else if($request->role == 'contributor'){
            $userid = 'CONT-'.$date. '-'.$six_digit_Id;
        }
        else if($request->role == 'helpdesk'){
            $userid = 'HELP-'.$date. '-'.$six_digit_Id;
        }
        else if($request->role == 'admin'){
            $userid = 'SYSA-'.$date. '-'.$six_digit_Id;
        }
        // dd($userid);
        
        
        // $user = User::create([
        //     'userid' => $abc,
        //     'six_digit_Id' => $last_id,
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);
        $profilepic = app('App\Http\Controllers\UploadImageController')->storage_upload($request->profilepic,'/app/public/Users/Profile/');
        $user = new User();
        $user->userid = $userid;
        $user->six_digit_Id = $six_digit;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->lock = 0;
        $user->wallet = 0;
        $user->number = $request->number;
        $user->profilepic = $profilepic;
        $user->password = Hash::make($request->password);
        $user->save();
        $role = Role::where('name',$request->role)->first();
        $user->roles()->attach($role);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            $user->roles()->detach();
            $user->delete();
            return redirect()->route('index');
        }
        else
        {
            if($user->hasRole('student'))
            {
                $user->roles()->detach();
                $user->delete();
                return redirect()->route('index');
            }
            else{
                return redirect()->route('index');

            }
        }
    }
}
