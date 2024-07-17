<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Enum\BaseCurrencyEnum;
use App\Service\FileService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Message\FetchCurrencyRatesMessage;
use App\Service\ExchangeRateService;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Utils\Currency;
use Symfony\Component\HttpFoundation\Request;

#[Route(path: '/rates')]
class RateController extends AbstractController
{

    public function __construct(
        private FileService $fileService
    ) {
    }

    /**
     *  Получить Курс валют для вашей валюты, по умолчанию USD
     */
    #[Route(path: '/{currencyCode}', name: 'app_rates', methods: 'GET')]
    public function index(MessageBusInterface $messageBus, string $currencyCode = ''): Response
    {
        $currencyCode = !empty($currencyCode) ? $currencyCode : BaseCurrencyEnum::getDefault()->value;
        $data = $this->fileService->getJson($currencyCode);

        // Если данных о данной валюте нет - пробуем отправить их загрузку в очередь.
        if (empty($data)) {

            $msg = sprintf('The "%s" rates you are looking for is not found. Please check if the currency code is correct.', $currencyCode);

            // Проверяем валидную ли валюту послал Клиент
            if (Currency::isAllowed($currencyCode)) {

                $msg = sprintf('The "%s" rates you are looking for is not found. Please try again after several minutes.', $currencyCode);

                $messageBus->dispatch(new FetchCurrencyRatesMessage($currencyCode));
            }

            throw new NotFoundHttpException($msg);
        }

        return $this->json(array_map(fn ($item, $code) => [
            "rate" => $item["rate"],
            "code" => $code,
        ], array_values($data), array_keys($data)));
    }



    /**
     * @Route("/convert", methods={"GET"})
     */
    #[Route(path: '/convert', name: 'app_convert', methods: 'POST')]
    public function convert(Request $request, ExchangeRateService $rateService): Response
    {
        $amount = (float) $request->query->get('amount');
        $from = $request->query->get('from');
        $to = $request->query->get('to');

        return $this->json($rateService->convert($amount, $from, $to));
    }
}
