<?php

namespace Api\Ctrl;

use Api\ApiAbstractCtrl;
use Core\WebService;

class ConfigCtrl extends ApiAbstractCtrl
{
    private function normalizeCfCountry(?string $h): ?string {
        if (!$h) return null;
        $code = strtoupper(trim($h));
        if (!preg_match('/^[A-Z]{2}$/', $code)) return null;
        if (in_array($code, ['XX','T1','A1'], true)) return null;
        return $code;
    }

    private function geoip_country_code_by_name($ip = null)
    {
      $cfCountry = $this->normalizeCfCountry($_SERVER['HTTP_CF_IPCOUNTRY'] ?? null);
      return $_SERVER['GEOIP_COUNTRY_CODE'] ?? $cfCountry ?? 'US';
    }

    public function loadConfig()
    {
        global $globalConfig;
        $lang = $globalConfig['staticParameters']['default_language'];
        if($lang == 'en') $lang = 'international';
        $keyValue = [
            'ir' => 'ir',
            'us' => 'international',
            'uk' => 'international',
        ];
        $countryFlag = strtolower($this->geoip_country_code_by_name(WebService::ipAddress()));
        if(isset($keyValue[$countryFlag])){
            $lang = $keyValue[$countryFlag];
        }
        $this->response = [
            'defaultLang' => $lang,
            'globalCssClass' => $globalConfig['staticParameters']['global_css_class'],
            'autoCheckTermsAndConditions' => true,
            'registrationRecommendedMinSecondsPast' => -3600,
            'showLoginAfterServerFinished' => true,
        ];
    }
}
