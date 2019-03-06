<?php

namespace HncProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $logged_in = false;
        $user_id = null;
        if ( null != $request->getSession()->get('token'))
        {
            //var_dump($request->getSession()->get('token')->getUser());
            //var_dump($request->getSession()->get('_security_main'));
            $token = $token = $this->get('session')->get('token');
            $user_id = $request->getSession()->get('user_id');
            $this->get('security.token_storage')->setToken($token);
            $logged_in = true;
        }

        $news = $this->get_JSON("https://newsapi.org/v2/top-headlines?language=en&country=gb&category=business&apiKey=ba3059047e3548fab44689b0b0870d93");
        $shares = null; //$this->share_json();
        $ftse_data = $this->ftse_json();

        return $this->render('@HncProject/Default/index.html.twig', ['logged_in' => $logged_in,
            'user_id' => $user_id, 'articles'=> $news->articles, 'shares' => $shares,
            'ftse_data' => $ftse_data]);
    }

    public function get_JSON($url)
    {
        return \Unirest\Request::get($url)->body;
    }

    //FTSE SHARES URL: https://spreadsheets.google.com/feeds/list/0AhySzEddwIC1dEtpWF9hQUhCWURZNEViUmpUeVgwdGc/1/public/basic?alt=json
    //ALPHAVANTAGE API KEY: QQ83MU4QXHBZ5LBM
    public function share_json($symbol)
    {
        $API_KEY = "QQ83MU4QXHBZ5LBM";
        $url = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=" . $symbol . "&outputsize=compact&apikey=" . $API_KEY;
        $share_json = $this->get_JSON($url);

        $rows = $share_json;


        return ['ShareData' => $share_json];
    }

    public function ftse_json()
    {
        $url = "https://spreadsheets.google.com/feeds/list/0AhySzEddwIC1dEtpWF9hQUhCWURZNEViUmpUeVgwdGc/1/public/basic?alt=json";

        $ftse_data = $this->get_JSON($url);

        $rows = $ftse_data->feed->entry;
        $t = '$t';
        foreach($rows as $row) {
            $pieces = explode(",", $row->content->$t);
            $ShareData[] = array(
                'Sym' => $row->title->$t,
                'Name' => substr($pieces[0], strpos($pieces[0], ":") + 1),
                'CurPrice' => substr($pieces[1], strpos($pieces[1], ":") + 1),
                'Chg' => substr($pieces[2], strpos($pieces[2], ":") + 1)
            );
        }

        return ['pieces' => $pieces, 'ShareData' => $ShareData];
    }
}
