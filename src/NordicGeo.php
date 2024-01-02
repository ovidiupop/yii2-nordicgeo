<?php
/**
 * Author: Antonio Ovidiu Pop
 * Date: 1/2/24
 * Filename: NordicGeo.php
 */

namespace ovidiupop\nordicgeo;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * NordicGeo is a Yii2 component for handling geographical API calls in Nordic countries.
 *
 * @property string $queryBaseUrl The base URL for API queries.
 * @property string $apisBaseUrl The base URL for API calls.
 * @property array $apis The array containing API configuration parameters.
 */
class NordicGeo extends Component
{
    /**
     * @var string $queryBaseUrl The base URL for API queries.
     */
    private $queryBaseUrl;

    /**
     * @var string $apisBaseUrl The base URL for API calls.
     */
    private $apisBaseUrl;

    /**
     * @var array $apis The array containing API configuration parameters.
     */
    private $apis;

    /**
     * Initializes the component and registers the necessary assets.
     */
    public function init()
    {
        parent::init();
        NordicGeoAsset::register(\Yii::$app->getView());
        $this->loadApis();
    }

    /**
     * Sets the base URL for API queries.
     *
     * @param string $queryBaseUrl
     */
    public function setQueryBaseUrl($queryBaseUrl)
    {
        $this->queryBaseUrl = $queryBaseUrl;
    }

    /**
     * Gets the base URL for API queries.
     *
     * @return string
     */
    public function getQueryBaseUrl()
    {
        return $this->queryBaseUrl;
    }

    /**
     * Gets the base URL for API calls.
     *
     * @return string
     */
    public function getApisBaseUrl()
    {
        return $this->apisBaseUrl;
    }

    /**
     * Sets the base URL for API calls.
     *
     * @param string $apisBaseUrl
     */
    public function setApisBaseUrl($apisBaseUrl)
    {
        $this->apisBaseUrl = $apisBaseUrl;
    }

    /**
     * Gets a list of countries with their codes and translated names.
     *
     * @return array An array containing country codes as keys and translated country names as values.
     */
    public function getCountries()
    {
        return [
            'DK' => Yii::t('app', 'Denmark'),
            'FI' => Yii::t('app', 'Finland'),
            'FO' => Yii::t('app', 'Faroe Islands'),
            'IS' => Yii::t('app', 'Iceland'),
            'NO' => Yii::t('app', 'Norway'),
            'SE' => Yii::t('app', 'Sweden'),
        ];
    }

    /**
     * Makes an API call based on the provided type, parameters, and optional combining flag.
     *
     * @param string $type The type of the API request.
     * @param array $params The parameters for the request.
     * @param bool $combine Whether to combine the results into an associative array.
     * @return array|false|mixed The result of the API request.
     */
    public function callApi($type, $params = [], $combine = false)
    {
        $url = $this->buildApiUrl($type, $params);
        $result = $this->makeApiCall($url);

        if ($combine && $type === 'Countries') {
            $countries = $this->getCountries(); // The getCountries function should return an array with country codes and names.
            $result = array_combine($result, array_map(function ($countryCode) use ($countries) {
                return $countries[$countryCode];
            }, $result));
        } elseif ($combine) {
            $result = array_combine($result, $result);
        }

        return $result;
    }


    /**
     * Builds the API URL based on the provided type and parameters.
     *
     * @param string $type The type of the API request.
     * @param array $params The parameters for the request.
     * @return string The constructed API URL.
     * @throws \Exception If a required parameter is missing.
     */
    private function buildApiUrl($type, $params)
    {
        $url = $this->queryBaseUrl . $type;
        $configParams = ArrayHelper::getValue($this->getParams(), "$type.params", []);

        foreach ($configParams as $param) {
            if (!isset($params[$param])) {
                throw new \InvalidArgumentException("Parameter '{$param}' is required for API call '{$type}'.");
            }
            $url .= '&' . $param . '=' . urlencode($params[$param]);
        }

        return $url;
    }

    /**
     * Loads API configuration parameters from the specified URL.
     */
    private function loadApis()
    {
        $url = $this->apisBaseUrl . 'params';
        $this->apis = json_decode(file_get_contents($url), true) ?: [];
    }

    /**
     * Gets the loaded API configuration parameters.
     *
     * @return mixed
     */
    public function getParams()
    {
        return $this->apis;
    }

    /**
     * Makes an API call to the specified URL and returns the decoded response.
     *
     * @param string $url The URL for the API call.
     * @return array|mixed The decoded API response.
     */
    private function makeApiCall($url)
    {
        $response = file_get_contents($url);
        return json_decode($response, true) ?: [];
    }
}