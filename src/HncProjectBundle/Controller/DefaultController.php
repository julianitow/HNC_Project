<?php

namespace HncProjectBundle\Controller;

use HncProjectBundle\Entity\Currency;
use HncProjectBundle\Entity\Portfolio;
use HncProjectBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $logged_in = false;
        $user_id = null;
        $symbol_result = null;
        $currency_user = null;
        if ( null != $request->getSession()->get('token'))
        {
            $token = $token = $this->get('session')->get('token');
            $user_id = $request->getSession()->get('user_id');
            $this->get('security.token_storage')->setToken($token);
            $logged_in = true;
            $currency_user = $this->get_logged_user()->getCurrency();
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
        $yesterday_2 = date('Y-m-d', strtotime( '-2 days' ));
        $yesterday_3 = date('Y-m-d', strtotime( '-3 days' ));

        $search_bar = $this->search_form($request);

        $time_series_daily = 'Time Series (Daily)';
        $meta_data = "Meta Data";
        $symbol = "2. Symbol";
        $volume = "5. volume";
        $close = "4. close";

        $sh_object = array();
        if ($search_bar['search_result'] != null)
        {
            //var_dump($this->share_json());
            foreach($search_bar['search_result'] as $result)
            {
                if (!property_exists($result, $time_series_daily))
                {
                    echo "<div class=\"alert alert-danger\">Sorry, API unreachable please try again later.</div>";
                    exit();
                }
                $data = $result->$time_series_daily;
                $symbol_result = $result->$meta_data->$symbol;
                //var_dump($search_bar['search_result']);

                if (property_exists($data, $today))
                {
                    $search_result = ['day' => $today, 'data' => $data->$today];
                }
                else if (property_exists($data, $yesterday))
                {
                    $search_result = ['day' => $yesterday, 'data' => $data->$yesterday];
                }
                else if (property_exists($data, $yesterday_2))
                {
                    $search_result = ['day' => $yesterday, 'data' => $data->$yesterday_2];
                }
                else if (property_exists($data, $yesterday_3))
                {
                    $search_result = ['day' => $yesterday, 'data' => $data->$yesterday_3];
                }
                else
                {
                    $search_result = null;
                }
                foreach($result as $res)
                {
                    foreach($res as $sh)
                    {
                        //var_dump($sh);
                        if(!is_string($sh))
                            array_push($sh_object, $sh);
                    }
                }
            }
        }
        else
        {
            $search_result = null;
        }

        //PURCHASE FORM
        $purchase = $this->purchase_form($request);
        //var_dump($purchase);

        return $this->render('@HncProject/Default/index.html.twig', ['logged_in' => $logged_in,
            'user_id' => $user_id, 'articles'=> $news->articles, 'search_form' => $search_bar['search_form']->createView(),
            'search_result_day' => $search_result['day'], 'search_result_data' => $search_result['data'], 'ftse_data' => $ftse_data,
            'error_code' => $error_code, 'symbol_result' => $symbol_result, 'sh' => $sh_object,
            'purchase_form' => $purchase['purchase_form']->createView(), 'user_currency' => $currency_user]);
    }

    public function get_JSON($url)
    {
        //$body = null;

        try
        {
            $body = \Unirest\Request::get($url)->body;
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
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
            ->add('search_input', SearchType::class, ['label' => false, 'required' => false, 'attr' => ['class' => 'form-control', 'placeholder' => "ex: MSFT"]])
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

    public function settingsAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $repository_user = $manager->getRepository('HncProjectBundle:User');
        $user = $repository_user->findOneBy(['id' => $this->get('session')->get('user_id')]);
        $portfolio_repository = $manager->getRepository('HncProjectBundle:Portfolio');
        //PORTFOLIO CREATION FORM
        $portfolio = new Portfolio();
        $portfolio_form_builder = $this->get('form.factory')->createNamedBuilder('portfolio_form', FormType::class, $portfolio, ['allow_extra_fields' => true]);
        $portfolio_form_builder
            ->add('submit', SubmitType::class);
        $portfolio_form = $portfolio_form_builder->getForm();
        $portfolio_form->handleRequest($request);

        if ($portfolio_form->isSubmitted() && $portfolio_form->isValid())
        {
            $portfolio->setUserId($user->getId());
            $manager->persist($portfolio);

            try
            {
                $manager->flush();
            }
            catch(\PDOException $e)
            {
                echo "ERROR" . $e->getMessage();
            }
        }

        $list_portfolio = $portfolio_repository->findBy(['userId' => $user->getId()]);

        $currency = new Currency();
        //$user = $repositoryUsers->findOneById($this->get('session')->get('user_id'));
        $currency_form_builder = $this->get('form.factory')->createNamedBuilder('currency_form', FormType::class, $currency, ['allow_extra_fields' => true]);
        $currency_form_builder
            ->add('name', CurrencyType::class)
            ->add('Currency_change', SubmitType::class);
        $currency_form = $currency_form_builder->getForm();
        $currency_form->handleRequest($request);

        $current_currency = $user->getCurrency();

        if ($currency_form->getClickedButton() && "Currency_change" == $currency_form->getClickedButton()->getName())
        {
            $currency_name = $_POST['currency_form']['name'];
            $user->setCurrency($currency_name);
            $manager->persist($user);

            try
            {
                $manager->flush();
            }
            catch(\PDOException $e)
            {
                echo "<div class=\"alert alert-danger\">" . $e->getMessage() . "</div>";
            }

        }

        $this->calcul_change_portfolio();
        return $this->render('@HncProject/Default/settings.html.twig', ['currency_form' => $currency_form->createView(), 'current_currency' => $current_currency,
        'portfolio_form' => $portfolio_form->createView(), 'portfolio_list' => $list_portfolio]);
    }

    public function purchase_form(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $portfolio_repository = $manager->getRepository('HncProjectBundle:Portfolio');
        $portfolio_choices = $portfolio_repository->findBy(['userId' => $this->get('session')->get('user_id')]);
        $transaction = new Transaction();
        $purchase_form_builder = $this->get('form.factory')->createNamedBuilder('purchase_form', FormType::class, $transaction, ['allow_extra_fields' => true]);
        $purchase_form_builder
            ->add('sharePrice', NumberType::class)
            ->add('share_name', TextType::class)
            ->add('volume_amount', NumberType::class)
            ->add('portfolioId', ChoiceType::class, ['choices' => $portfolio_choices])
            ->add('price', NumberType::class)
            ;
        $purchase_form = $purchase_form_builder->getForm();
        $purchase_form->handleRequest($request);


        if ($purchase_form->isSubmitted() && $purchase_form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $transaction = $purchase_form->getData();
            $transaction->setUserId($this->get('session')->get('user_id'));
            $transaction->setPortfolioId($transaction->getPortfolioId()->getId());

            $transaction->setDate(new \DateTime('today'));

            $portfolio_concerned = $portfolio_repository->findOneBy(['id' => $transaction->getPortfolioId()]);
            $portfolio_concerned->setTotalAmount($portfolio_concerned->getTotalAmount() + $transaction->getPrice());
            $manager->persist($transaction);
            $manager->persist($portfolio_concerned);

            try
            {
                $manager->flush();
            }
            catch(\PDOException $e)
            {
                echo $e->getMessage();
            }
        }

        return ['purchase_form' => $purchase_form];
    }

    public function get_logged_user()
    {
        $manager = $this->getDoctrine()->getManager();
        $repository_user = $manager->getRepository('HncProjectBundle:User');
        $user = $repository_user->findOneBy(['id' => $this->get('session')->get('user_id')]);

        return $user;
    }

    public function calcul_change_portfolio()
    {
        $user_id = $this->get('session')->get('user_id');
        $manager = $this->getDoctrine()->getManager();
        $portfolio_repository = $manager->getRepository('HncProjectBundle:Portfolio');
        $transaction_repository = $manager->getRepository('HncProjectBundle:Transaction');
        $portfolio_list = $portfolio_repository->findAll();
        $transaction_list = $transaction_repository->findAll();

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime( '-1 days' ));
        $yesterday_2 = date('Y-m-d', strtotime( '-2 days' ));
        $yesterday_3 = date('Y-m-d', strtotime( '-3 days' ));

        $time_series_daily = 'Time Series (Daily)';
        $close = "4. close";

        foreach($portfolio_list as $portfolio)
        {
            foreach($transaction_list as $transaction)
            {
                if ($transaction->getId() == $portfolio->getId())
                {
                    foreach($this->share_json($transaction->getShareName()) as $result) {

                        if (!property_exists($result, $time_series_daily))
                        {
                            echo "<div class=\"alert alert-danger\">Sorry, API unreachable please try again later.</div>";
                            return;
                        }
                        $data = $result->$time_series_daily;

                        if (property_exists($data, $today)) {
                            $share_result = ['portfolio' => $portfolio->getId(), 'data' => $data->$today->$close];
                        } else if (property_exists($data, $yesterday)) {
                            $share_result = ['portfolio' => $portfolio->getId(), 'data' => $data->$yesterday->$close];
                        } else if (property_exists($data, $yesterday_2)) {
                            $share_result = ['portfolio' => $portfolio->getId(), 'data' => $data->$yesterday_2->$close];
                        } else if (property_exists($data, $yesterday_3)) {
                            $share_result = ['portfolio' => $portfolio->getId(), 'data' => $data->$yesterday_3->$close];
                        } else {
                            $search_result = null;
                        }

                        echo $portfolio->getId() . "-" . $portfolio->getUserId() . " " . $share_result['data'] . " -- " . $transaction->getSharePrice() . "| <br>";
                    }
                }
            }
        }
    }
}
