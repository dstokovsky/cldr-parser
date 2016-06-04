<?php

require_once __DIR__ . '/vendor/autoload.php';

use CommerceGuys\Intl\Language\LanguageRepository;
use CommerceGuys\Intl\Exception\UnknownLanguageException;

$xmlSource = 'http://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml';
$data = new SimpleXMLElement($xmlSource, NULL, true);
$languages = [];
foreach ($data->territoryInfo->territory as $territory) {
    foreach ($territory->languagePopulation as $lang) {
        if (strstr((string)$lang['type'], '_')) {
            $languageCode = explode('_', (string)$lang['type'])[0];
        } else {
            $languageCode = (string)$lang['type'];
        }
        if (isset($languages[$languageCode])) {
            $languages[$languageCode] += floor((float)$territory['population'] * (float)$lang['populationPercent'] / 100);
        } else {
            $languages[$languageCode] = floor((float)$territory['population'] * (float)$lang['populationPercent'] / 100);
        }
    }
}
arsort($languages);
$languageRepository = new LanguageRepository;
foreach ($languages as $code => $speakers) {
    if (empty($speakers)) {
        continue;
    }
    try {
        $language = $languageRepository->get($code);
        print sprintf("%s %s %d" . PHP_EOL, $language->getLanguageCode(), $language->getName(), $speakers);
    } catch(UnknownLanguageException $e) {
        
    }
}
