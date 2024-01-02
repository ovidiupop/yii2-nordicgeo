Yii2 NordicGeo
=============

Yii2-NordicGeo is a robust Yii2 component designed for seamless integration with geographical APIs in the Nordic countries.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist ovidiupop/yii2-nordicgeo "~1.0"

```

or add

```
"ovidiupop/yii2-nordicgeo": "~1.0"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, integrate the NordicGeo component into your Yii2 application by configuring it in config.php. Harness the power of APIs from the nordic-geo.com:
```php
'components' => [
    'nordicGeo' => [
        'class' => 'ovidiupop\nordicgeo\NordicGeo',
        'apisBaseUrl' => 'http://nordic-geo.com/',
        'queryBaseUrl' => 'http://nordic-geo.com/api/query?type=',
    ],
    ............
],

'controllerMap' => [
    'nordicgeo'=> 'ovidiupop\nordicgeo\controllers\NordicGeoController',
    ............
]
```

To use autocompletion with dependent data in a form, you need to add specific classes for each field.  
These are:  
'geography-select country' - pentru select2 country  
'geography-select.region' - pentru select2 region  
'geography-select place' pentru select2 city  
'geography-select postalcode' - pentru select2 postal code  

```php
    <?php echo $form->field($model, 'country')->widget(Select2::classname(), [
        'data' =>  NordicGeoController::cmb('Countries', []),
        'options' => [
            'prompt' => Yii::t('app', 'Select country'),
            'class'=>'geography-select country'
        ],
    ]);?>
    <?php echo $form->field($model, 'region')->widget(Select2::classname(), [
        'data' =>  NordicGeoController::cmb('RegionsByCountry', ['country'=> $model->country]),
        'options' => [
            'prompt' => Yii::t('app', 'Select region'),
            'class'=>'geography-select region'
        ],
    ]);?>
    <?php echo $form->field($model, 'city')->widget(Select2::classname(), [
        'data' =>  NordicGeoController::cmb('PlacesByRegion', ['country'=>$model->country, 'region'=>$model->region]),
        'options' => [
            'prompt' => Yii::t('app', 'Select city'),
            'class'=>'geography-select place'
        ],
    ]);?>
    <?php echo $form->field($model, 'postalCode')->widget(Select2::classname(), [
        'data' =>  NordicGeoController::cmb('PostalCode', ['country'=>$model->country, 'region'=>$model->region, 'place'=>$model->city]),
        'options' => [
            'prompt' => Yii::t('app', 'Select postal code'),
            'class'=>'geography-select postalcode'
        ],
    ]);?>

```

In the used model, to bypass the validation of the mandatory "region" field when the selected country is Iceland or the Faroe Islands, assuming the model uses the names "country" for the country and "region" for the region, modify the rules as follows:

```php
            ['region', 'required', 'when' => function ($model) {
                return !in_array($model->country, ['IS', 'FO']);
            }, 'whenClient' => "function (attribute, value) {
                return !(['IS', 'FO'].includes($('.geography-select.country').val()));
            }"],
```
