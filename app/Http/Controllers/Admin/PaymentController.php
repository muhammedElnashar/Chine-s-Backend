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

        Payment::create($data);

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
    public function update(Request $request,Payment $payment)
    {
        $data=$request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|numeric|min:0',
        ]);
        $data['paid_at']=now();
        $payment->update($data);
        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
