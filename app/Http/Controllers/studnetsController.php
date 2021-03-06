<?php

namespace App\Http\Controllers;

use App\AcademicYear;
use App\SchoolClass;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;


use App\Student;

class studnetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('students.index');
    }

    public function user(){
        $user = Hash::make('admin123');
        echo $user;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    


        $academic_info  = DB::table('academic_years')->select('name', 'id')->where('status','=', '1')->get();

        if($academic_info->isEmpty()){

            $academic_year_info = AcademicYear::all();
            return redirect('academic_year')->with('academic_year_info', $academic_year_info)
                                                ->with('error', 'قم بتشغل عام دراسي معين !!');;
        }

        else {
            
            foreach ($academic_info as $academinc_year) {
                $academinc_year_id = $academinc_year->id;
                $academinc_year_name = $academinc_year->name;
            }

            $classes = SchoolClass::all();
            return view('students.create')->with('classes', $classes)
                                          ->with('academic_year_id', $academinc_year_id)
                                          ->with('academic_year_name', $academinc_year_name);
        }
   

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        if ($request->hasFile('pic')) {
            $pic = $request->pic;
            $pic_new_name = time().$pic->getClientOriginalName();
            $pic->move('public/upload/students', $pic_new_name);
        }
        else
        {
            $pic_new_name = 'default.png';
        }
        


        $student_no = Student::all();

        /** To generate the reg_no */
        $student_reg_no = $student_no->count()+1;
        $student_reg_numbers = (string)  $student_reg_no;

        $reg_no = "00$student_reg_numbers";

        /** To generate the reg_no */
        
       
        $student = Student::create([
            'academic_years_id'=> $request->academic_year_id,
            'name'=> $request->name,
            'dob'=> $request->dob,
            'gender'=> $request->gender,
            'relegion'=>$request->relegion,
            'blood'=> $request->blood,
            'nationality'=>$request->nationality,
            'pic'=>'/upload/posts/'.$pic_new_name,
            'std_note'=>$request->std_note,
            'fa_name'=>$request->fa_name,
            'ma_name'=>$request->ma_name,
            'fa_phone'=>$request->fa_phone,
            'address'=>$request->address,
            'reg_no'=>$reg_no,
            'class_id'=>$request->class_id,
            'fees'=>$request->fees,
            'reg_fees'=>$request->reg_fees,
            'bus_fees'=>$request->bus_fees,
            'fees_note'=>$request->fees_note,
            
        ]);

        return redirect()->route('students')->with('success', 'تمت إضافة الطالب بنجاح.');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $classes = SchoolClass::all();
        $student = Student::find($id);
        return view('students.show')->with('student', $student)->with('classes', $classes);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::find($id);
        $classes = SchoolClass::all();
        return view('students.edit')->with('student', $student)->with('classes', $classes);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);

        if($request->hasFile('pic')){

            $pic = $request->pic;
            $pic_new_name = time().$pic->getClientOriginalName();
            $pic->move('public/upload/students', $pic_new_name);
            $student->pic = '/upload/students/'.$pic_new_name;

        }


        if ($request->delete_pic)
        {
            $student->pic = 'public/upload/students/'.$request->delete_pic;
        }

        $student->name = $request->name;
        $student->dob = $request->dob;
        $student->gender = $request->gender;
       


        $student->relegion = $request->relegion;
        $student->blood = $request->blood;
        $student->nationality = $request->nationality;


       
        $student->std_note = $request->std_note;
        $student->fa_name = $request->fa_name;



        $student->ma_name = $request->ma_name;
        $student->fa_phone = $request->fa_phone;
        $student->address = $request->address;



        $student->reg_no = $request->reg_no;
        $student->class_id = $request->class_id;
        $student->fees = $request->fees;


        $student->reg_fees = $request->reg_fees;
        $student->bus_fees = $request->bus_fees;
        $student->fees_note = $request->fees_note;

        $student->save();

        //$student>tags()->sync($request->tags);

        return redirect()->route('students')->with('success', 'تم تعديل بيانات الطالب.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        $student->delete();
        return back()->with('delete', 'تم مسح بيانات الطالب - يمكنك مراجعه سلة المهملات');
    }


    public function studentTrashed(){

        return view('students.trashed.index');
    }

    public function restore($id){
        $student = Student::withTrashed()->where('id', $id)->first();
        $student->restore();
        return redirect()->route('studentTrashed')->with('success', 'تم إسترجاع بيانات الطالب.');
    }
}
