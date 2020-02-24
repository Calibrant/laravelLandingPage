<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Page;
use App\Service;
use App\Portfolio;
use App\People;
use DB;
use Mail;

class IndexController extends Controller
{
    public function execute(Request $request)
    {

        if ($request->isMethod('post')) {

            $messages = [
                'required' => 'Поле :attribute обязательно к заполнения',
                'email' => 'Поле :attribute должно соответствовать email адресу'
            ];

            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email',
                'text' => 'required'
            ], $messages);

            $data = $request->all();
            Mail::send('site.email', ['data' => $data], function ($message) use ($data) {
                $mail_admin = env('MAIL_ADMIN');
                $message->from($data['email'], $data['name']);
                $message->to($mail_admin,'Mr. Admin')->subject('Question');

            });

            session(['status'=>'Email is send']);
            return redirect()->route('home');
//            if ($result){
//                return redirect()->route('home')->with('status','Email is send');
//            }
//            $request->session()->flash('status', 'Email is sent');
//            return redirect()->route('home');

        }

        $pages = Page::all();
        $portfolios = Portfolio::get(array('name', 'filter', 'images'));
        $services = Service::where('id', '<', 20)->get();
        $peoples = People::take(3)->get();

        //конструктор запросв
        //получаем уник. знач. из бд колонки фильтр
        $tags = DB::table('portfolios')->distinct()->pluck('filter');

        // dd($tags);

        $menu = array();
        foreach ($pages as $page) {
            $item = array('title' => $page->name, 'alias' => $page->alias);
            array_push($menu, $item);
        }
        $item = array('title' => 'Services', 'alias' => 'service');
        array_push($menu, $item);

        $item = array('title' => 'Portfolio', 'alias' => 'Portfolio');
        array_push($menu, $item);

        $item = array('title' => 'Team', 'alias' => 'team');
        array_push($menu, $item);

        $item = array('title' => 'Contact', 'alias' => 'contact');
        array_push($menu, $item);

        return view('site.index', array(
            'menu' => $menu,
            'pages' => $pages,
            'services' => $services,
            'portfolios' => $portfolios,
            'peoples' => $peoples,
            'tags' => $tags,
        ));
    }
}


/*
2 года назад
Проверяю на вер. 5.5
Не обязательно смотреть через  dd();
Можно передать результат выборки сразу в шаблон на отображение:
return view('welcome', ['page' => $page, 'portfolio'=>$portfolio, 'service'=>$service, 'people'=>$people]);
и в шаблон добавить:
<pre>
@foreach($page as $pag)
{{ $pag }}
    @endforeach
</pre>
<pre>
@foreach($portfolio as $portfol)
{{ $portfol }}
    @endforeach
</pre>
<pre>
@foreach($service as $serv)
{{ $serv }}
    @endforeach
</pre>
<pre>
@foreach($people as $pipl)
{{ $pipl }}
    @endforeach
</pre>*/

