<?php

namespace HncProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $ftse_data = $this->ftse_json();

        $error_code = null;
        if ($news == null | $ftse_data == null)
        {
            //Source unnreachable
            $error_code = "error_connection_01";
        }

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime( '-1 days' ));

        $search_bar = $this->search_form($request);

        $time_series_daily = 'Time Series (Daily)';
        if ($search_bar['search_result'] != null)
        {
            foreach($search_bar['search_result'] as $result)
            {
                $data = $result->$time_series_daily;

                if (property_exists($data, $today))
                {
                    $search_result = ['day' => $today, 'data' => $data->$today];
                }
                else if (property_exists($data, $yesterday))
                {
                    $search_result = ['day' => $yesterday, 'data' => $data->$yesterday];
                }
            }
        }
        else
        {
            $search_result = null;
        }

        return $this->render('@HncProject/Default/index.html.twig', ['logged_in' => $logged_in,
            'user_id' => $user_id, 'articles'=> $news->articles, 'search_form' => $search_bar['search_form']->createView(),
            'search_result_day' => $search_result['day'], 'search_result_data' => $search_result['data'], 'ftse_data' => $ftse_data, 'error_code' => $error_code]);
    }

    public function get_JSON($url)
    {
        $body = null;

        try
        {
            $body = \Unirest\Request::get($url)->body;
        }
        catch (\Exception $e)
        {
            $e->getMessage();
        }
        return $body;
    }

    //FTSE SHARES URL: https://spreadsheets.google.com/feeds/list/0AhySzEddwIC1dEtpWF9hQUhCWURZNEViUmpUeVgwdGc/1/public/basic?alt=json
    //ALPHAVANTAGE API KEY: QQ83MU4QXHBZ5LBM
    public function share_json($symbol)
    {
        $API_KEY = "QQ83MU4QXHBZ5LBM";
        $url = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=" . $symbol . "&outputsize=compact&apikey=" . $API_KEY;
        $share_json = $this->get_JSON($url);

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

    public function search_form(Request $request)
    {
        $share_data = null;

        $search_form_Builder = $this->createFormBuilder();
        $search_form_Builder
            ->add('search_input', SearchType::class, ['label' => false, 'attr' => ['class' => 'form-control', 'placeholder' => "ex: MSFT"]])
            ->add('search_btn', SubmitType::class, ['label' => "Search", 'attr' => ['class' => 'btn btn-success']])
        ;
        $search_form = $search_form_Builder->getForm();
        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid())
        {
            $search_data = $search_form->getData();
            $share_data = $this->share_json($search_data['search_input']);
            $error_message = 'Error Message';

            if (property_exists($share_data['ShareData'], $error_message))
            {
                $share_data = null;
            }
        }

        return ['search_form' =>$search_form, 'search_result' => $share_data];
    }
}
