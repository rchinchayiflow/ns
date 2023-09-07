<?php

declare(strict_types=1);

namespace App\Service\V1;

use App\Config\IflowConfig;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IflowApiService
{
    private $client;

    private $config;

    public function __construct(
        Client $client,
        IflowConfig $config
    ) {
        $this->client = $client;
        $this->config = $config;
    }

    public function getToken(Request $request)
    {
        try {
            $url    = $this->config->getUrlLogin();
            $params = [
                '_username' => $request->input('username'),
                '_password' => $request->input('password'),
                'headers'   => [
                    'Content-Type' => 'application/json',
                    'Cookie'       => 'SERVERID=api_iflow21',
                ],
            ];

            $response = $this->client->post(
                $url,
                [
                    'json' => $params,
                ]
            );

            $requestData = $request->only(['username', 'password']);

            $responseContent = $response->getBody();

            $responseArray = json_decode((string) $responseContent, true);

            if ($responseArray['success'] === false) {
                throw new \Exception($responseArray['message'].json_encode($requestData));
            }

            Log::channel('api_iflow')->info('respuesta de la api: '.$responseContent);
            $token = $responseArray['token'] ?? null;

            return $token;
        } catch (\Throwable $th) {
            Log::channel('api_iflow')->error('Respuesta de la api: '.$th->getMessage());

            throw new \Exception('Error al consumir la api de logueo de iflow');
        }
    }

    /**
     * Método para obtener el estado de un pedido en la API de Iflow.
     *
     * @param string $trackId El identificador del pedido en el sistema de Iflow.
     * @param string $token El token de autenticación para acceder a la API de Iflow.
     *
     * @throws \Exception Si la API de Iflow devuelve una respuesta no exitosa o si ocurre cualquier otra excepción.
     * @return array $order_status El estado del pedido, como un arreglo asociativo.
     */
    public function getStatusOrder(string $trackId, string $token)
    {
        try {
            $url = $this->config->getUrlStatusOrder().'/'.$trackId;

            $response = $this->client->get(
                $url,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                    ],
                ]
            );

            $responseContent = $response->getBody();
            $responseArray   = json_decode((string) $responseContent, true);

            Log::channel('api_iflow')->info('Respuesta de la api: '.$responseContent);

            return $responseArray;
        } catch (\Throwable $th) {
            Log::channel('api_iflow')->error('Respuesta de la api: '.$th->getMessage());

            throw new \Exception('Error al obtener el estado del pedido en iflow');
        }
    }

    /**
     * Método para obtener todas las órdenes de un vendedor en la API de Iflow.
     *
     * @param string $token El token de autenticación para acceder a la API de Iflow.
     *
     * @throws \Exception Si la API de Iflow devuelve una respuesta no exitosa o si ocurre cualquier otra excepción.
     * @return array $seller_orders Todas las órdenes del vendedor, como un arreglo asociativo.
     */
    public function getSellerOrders(string $token)
    {
        try {
            $url = $this->config->getUrlSellerOrders();

            $response = $this->client->get(
                $url,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Content-Type'  => 'application/json',
                        'Cookie'        => 'Cookie_1=value; SERVERID=api_iflow21_n2',
                    ],
                ]
            );

            $responseContent = $response->getBody();
            $responseArray   = json_decode((string) $responseContent, true);

            Log::channel('api_iflow')->info('Respuesta de la api: '.$responseContent);

            return $responseArray;
        } catch (\Throwable $th) {
            Log::channel('api_iflow')->error('Respuesta de la api: '.$th->getMessage());

            throw new \Exception('Error al obtener las ordenes del vendedor en iflow');
        }
    }
}
