<?php

namespace App\Http\Controllers\Admin;

use App\Enum\CourseTypeEnum;
use App\Enum\MethodEnum;
use App\Enum\StatusEnum;
use App\Enum\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['user', 'course'])->latest()->paginate(10);
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role',UserRoleEnum::User)->get();
        $courses = Course::where('type', CourseTypeEnum::Paid)->get();
        return view('payments.create' , compact('users', 'courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $data=$request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|numeric|min:0',
        ]);
       $data['status']= StatusEnum::Completed;
       $data['payment_method']= MethodEnum::Manual;
       $data['paid_at']=now();

        if (Payment::where('user_id', $data['user_id'])->where('course_id', $data['course_id'])->exists()) {
            return redirect()->back()->with('error','This user already paid for this course.');
        }

        DB::transaction(function () use ($data) {
            Payment::create($data);
            $user = User::findOrFail($data['user_id']);
            $user->subscribeToCourse($data['course_id'], $data['paid_at']);
        });
        return redirect()->route('payments.index')->with('success', 'Payment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $users = User::where('role', UserRoleEnum::User)->get();
        $courses = Course::where('type', CourseTypeEnum::Paid)->get();
        return view('payments.edit', compact('payment', 'users', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'amount'    => 'required|numeric|min:0',
        ]);

        $data['paid_at'] = now();

        DB::transaction(function () use ($payment, $data) {

            $originalUserId = $payment->user_id;
            $originalCourseId = $payment->course_id;

            $payment->update($data);

            if ($originalUserId != $data['user_id'] || $originalCourseId != $data['course_id']) {

                $oldUser = User::find($originalUserId);
                if ($oldUser) {
                    $oldUser->courses()->detach($originalCourseId);
                }
                $newUser = User::findOrFail($data['user_id']);
                $newUser->subscribeToCourse($data['course_id'], $data['paid_at']);
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment Update Successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $user = User::find($payment->user_id);

            if ($user) {
                $user->courses()->detach($payment->course_id);
            }

            $payment->delete();
        });
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
