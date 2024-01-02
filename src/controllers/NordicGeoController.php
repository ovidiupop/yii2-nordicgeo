<?php
/**
 * Author: Antonio Ovidiu Pop
 * Date: 1/2/24
 * Filename: NordicGeoController.php
 */

namespace ovidiupop\nordicgeo\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * NordicGeoController handles geographical API-related actions.
 */
class NordicGeoController extends Controller
{
    /**
     * Retrieves a combined array for a specified type and parameters using the NordicGeo component.
     *
     * @param string $type The type of the API request.
     * @param array $params The parameters for the API request.
     * @return array The combined array result from the API call.
     * @throws \Exception If required parameters are missing.
     */
    public static function cmb($type, $params)
    {
        $apiCaller = Yii::$app->nordicgeo;
        $defaultParams = ArrayHelper::getValue($apiCaller->getParams(), "$type.params", []);

        foreach ($defaultParams as $defaultParam) {
            if (!array_key_exists($defaultParam, $params) || !$params[$defaultParam]) {
                return [];
            }
        }

        return Yii::$app->nordicgeo->callApi($type, $params, true);
    }

    /**
     * Action to retrieve options based on the provided API type and parameters.
     *
     * @return string The HTML options based on the API call result.
     */
    public function actionGetOptions()
    {
        $params = Yii::$app->request->queryParams;

        if (isset($params['type']) && $params['type']) {
            $type = $this->correctTypeForCountries($params['type'], $params);
            return self::arrayToOptions(self::cmb($type, $params));
        }

        return '';
    }

    /**
     * Corrects the API type for countries 'IS' (Iceland) and 'FO' (Faroe Islands).
     *
     * @param string $type The original API type.
     * @param array $params The parameters for the API request.
     * @return string The corrected API type.
     */
    private function correctTypeForCountries($type, $params)
    {
        if ($type != 'PlacesByRegion' && $type != 'PostalCode') {
            return $type;
        }

        $country = $params['country'];
        $type = ($country === 'IS' || $country === 'FO')
            ? ($type === 'PlacesByRegion' ? 'PlacesByCountry' : 'PostalCodeByPlace')
            : $type;

        return $type;
    }

    /**
     * Converts an associative array to HTML option tags.
     *
     * @param array $array The associative array to convert.
     * @return string The HTML options.
     */
    public static function arrayToOptions($array)
    {
        $html = '';
        foreach ($array as $key => $value) {
            $html .= '<option value="' . htmlspecialchars($key) . '">' . htmlspecialchars($value) . '</option>';
        }
        return $html;
    }
}
