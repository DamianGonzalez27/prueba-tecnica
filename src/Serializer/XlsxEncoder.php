<?php

namespace App\Serializer;

use App\Helpers\ExcelReportGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class XlsxEncoder implements EncoderInterface, DecoderInterface
{

    /**
     * @var ExcelReportGenerator
     */
    private $excelReportGenerator;

    /**
     * @var RequestStack $requestStack
     */
    private $requestStack;

    public function __construct(ExcelReportGenerator $excelReportGenerator, RequestStack $requestStack)
    {
        $this->excelReportGenerator = $excelReportGenerator;
        $this->requestStack = $requestStack;
    }

    public function encode($data, $format, array $context = [])
    {
        foreach ($data as &$value) {
            $flattened = [];
            $this->flatten($value, $flattened, '.', '');
            $value = $flattened;
        }
        unset($value);

        $reportConfig = $this->requestStack->getCurrentRequest()->query->get('reportConfig');
        $config = $reportConfig?json_decode($reportConfig,true):[];

        $writer = $this->excelReportGenerator->generate($data, $config);

        $reportName = isset($config['name'])?$config['name']:'report.xlsx';

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Type:  application/octet-stream');
        header('Cache-Control: max-age=0');
        header('Content-Disposition: attachment;filename="'.$reportName.'"');


        ob_end_clean();
        $writer->save('php://output');
        die;
    }

    public function supportsEncoding($format)
    {
        return 'xlsx' === $format;
    }

    public function decode($data, $format, array $context = [])
    {
        return null;
    }

    public function supportsDecoding($format)
    {
        false;
    }


    /**
     * Flattens an array and generates keys including the path.
     */
    private function flatten(array $array, array &$result, string $keySeparator, string $parentKey = '')
    {
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $this->flatten($value, $result, $keySeparator, $parentKey.$key.$keySeparator);
            } else {
                // Ensures an actual value is used when dealing with true and false
                $result[$parentKey.$key] = false === $value ? 0 : (true === $value ? 1 : $value);
            }
        }
    }
}

