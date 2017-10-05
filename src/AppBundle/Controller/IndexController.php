<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        if ($request->isMethod(Request::METHOD_GET)) {
            $id   = $request->query->get('id');

            $url  = "https://test.oppwa.com/v1/checkouts/{$id}/payment";
            $url .= "?authentication.userId=8a829418530df1d201531299e2c117aa";
            $url .= "&authentication.password=g2gSpgKhKS";
            $url .= "&authentication.entityId=8a829418530df1d201531299e097175c";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if(curl_errno($ch)) {
                return curl_error($ch);
            }
            curl_close($ch);


            dump(json_decode($responseData)); exit;
        }

        $url = "https://test.oppwa.com/v1/checkouts";

        $output = $this->normalizeProductUrl([
            '8a829418530df1d201531299e2c117aa', // USER ID
            'g2gSpgKhKS',                       // PASSWORD
            '8a829418530df1d201531299e097175c', // ENTITY ID
            '20.00',                            // AMOUNT
            'EUR',                              // CURRENCY
            'DB'                                // PAYMENT TYPE
        ]);


        $data = $this->make($url, $output);

        return $this->render('@App/index.html.twig', [
            'checkoutId' => $data['id']
        ]);
    }

    /**
     * @param  array $data
     *
     * @return string
     */
    private function normalizeProductUrl(array $data)
    {
        list($userId, $pass, $entityId, $amount, $currency, $paymentType) = $data;

        $output =
            "authentication.userId={$userId}" .
            "&authentication.password={$pass}" .
            "&authentication.entityId={$entityId}" .
            "&amount={$amount}" .
            "&currency={$currency}" .
            "&paymentType={$paymentType}"
        ;

        return $output;
    }

    /**
     * @param  string $url
     * @param  string $output
     *
     * @return string
     */
    private function make($url, $output)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $output);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);

        if(curl_errno($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);

        return json_decode($responseData, true);
    }
}
