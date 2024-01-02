<?php
/**
 * Author: Antonio Ovidiu Pop
 * Date: 1/2/24
 * Filename: NordicGeoAsset.php
 */
namespace ovidiupop\nordicgeo;

use yii\web\AssetBundle;
class NordicGeoAsset extends AssetBundle{
    public $sourcePath;
    public $js;

    public function init(){
        parent::init();
        $this->sourcePath = __DIR__ . '/assets/';
        $this->js = [
            'nordicgeo.js'
        ];
    }
    public $depends = [
        'yii\web\YiiAsset',
    ];
}