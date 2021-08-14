<?php

namespace App\Maison;

use Illuminate\Http\Request; // Important pour accéder à Request!
use Illuminate\Support\Facades\DB;
use Mail;
use Illuminate\Support\Facades\Redirect;
use Log;

/**
 * Fonctions de cryptographie maison, pour masquer les id réelles dans les url
 *
 * @author lsd
 */
class Calculs {
    
    public $base62 = "0123456789aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ";
    
    private $fiv = 'thesixreload';
    private $cle = '';
    
    public function __construct() {
        $this->cle = hex2bin(config('perso.cle')); // 16 octets
    }
    
    public function tokenize($param, $iv = null) {
        $b62 = $this->base62;
        $iv = $iv ? $iv : bin2hex(random_bytes(2)); // 4 caractères
        $fiv = $this->fiv;
        $token = openssl_encrypt($param, "aes-128-cbc", $this->cle, OPENSSL_RAW_DATA, $fiv.$iv);
        //$token = strtr($token, '+/=', '-_,');
        return $this->convBase(bin2hex($token.$iv), "0123456789abcdef", $b62);
        //return $this->str_baseconvert(bin2hex($token.$iv), 16, 36);
    }
    
    public function detokenize($token) {
        $b62 = $this->base62;
        //$token = hex2bin($this->str_baseconvert($token, 36, 16));
        try {
            $token = hex2bin($this->convBase($token, $b62, "0123456789abcdef"));
            $iv = substr($token, -4);
            $token = substr($token, 0, -4);
            //$token = strtr($token, '-_,', '+/=');
            $fiv = $this->fiv;
            $original = openssl_decrypt($token, "aes-128-cbc", $this->cle, OPENSSL_RAW_DATA, $fiv . $iv);
        } catch (\Exception $e) {
            return false;
        }
        return $original;
    }
    
    public function rotateString($string, $n) {
        $l = strlen($string);
        $n = $n % $l;
        $rotated = substr($string, $n) . substr($string, 0, $n);
        return $rotated;
    }
    
    public function convBase($numberInput, $fromBaseInput, $toBaseInput) {
        if ($fromBaseInput == $toBaseInput)
            return $numberInput;
        $fromBase = str_split($fromBaseInput, 1);
        $toBase = str_split($toBaseInput, 1);
        $number = str_split($numberInput, 1);
        $fromLen = strlen($fromBaseInput);
        $toLen = strlen($toBaseInput);
        $numberLen = strlen($numberInput);
        $retval = '';
        if ($toBaseInput == '0123456789') {
            $retval = 0;
            for ($i = 1; $i <= $numberLen; $i++)
                $retval = bcadd($retval, bcmul(array_search($number[$i - 1], $fromBase), bcpow($fromLen, $numberLen - $i)));
            return $retval;
        }
        if ($fromBaseInput != '0123456789')
            $base10 = $this->convBase($numberInput, $fromBaseInput, '0123456789');
        else
            $base10 = $numberInput;
        if ($base10 < strlen($toBaseInput))
            return $toBase[$base10];
        while ($base10 != '0') {
            $retval = $toBase[bcmod($base10, $toLen)] . $retval;
            $base10 = bcdiv($base10, $toLen, 0);
        }
        //if ($toBaseInput == "0123456789abcdef" && (strlen($retval) % 2)) {
        //    $retval = "0".$retval;
        if ($toBaseInput == "0123456789abcdef") {
            $fac = ceil($numberLen / 32);
            $minlen = 8 + ($fac * 32);
            $retval = str_pad($retval, $minlen, "0", STR_PAD_LEFT);
        }
        return $retval;
    }
    
    function convertitNombreEnLettres($nombreAConvertir) {
        $tablePuissancesDeDix = array("", "mille", "millions", "milliards"); // ce qu'on affichera apres chaque serie de trois
        $tableConversionEtapeDeux = array("", "dix", "vingt", "trente", "quarante", "cinquante", "soixante", "septante", "quatre-vingt", "nonente"); // equivalent du second chiffre de la serie de 3 (la dizaine)
        $tableConversionEtapeUnOuTrois = array("", "un", "deux", "trois", "quatre", "cinq", "six", "sept", "huit", "neuf"); // equivalent du premier et du troisieme chiffre de la serie de 3
        $tableConversionDeLaDizaineQuiFaitChier = array("", "onze", "douze", "treize", "quatorze", "quinze", "seize");
        $nombreAConvertir = number_format($nombreAConvertir); // on formate le nombre "a l'anglaise" avec des virgules entre les milliers
        $tableauTemporaire = explode(',', $nombreAConvertir); // on passe les milliers dans un tableau
        for ($i = 0; $i < count($tableauTemporaire); $i++) {// on parcourt le tableau, par milliers donc
            for ($j = 0; $j < strlen($tableauTemporaire[$i]); $j++) {// on parcourt les 3 caracteres (ou moins) du millier en cours
                switch ($j) {
                    case strlen($tableauTemporaire[$i]) - 3://si on est dans les centaines
                        if (substr($tableauTemporaire[$i], $j, 1) > 0) {
                            if (substr($tableauTemporaire[$i], $j, 1) > 1)
                                echo $tableConversionEtapeUnOuTrois[substr($tableauTemporaire[$i], $j, 1)];
                            echo " cents ";
                        }
                        break;
                    case strlen($tableauTemporaire[$i]) - 2:// si on est dans les dizaines
                        if (substr($tableauTemporaire[$i], $j, 1) > 1)
                            echo $tableConversionEtapeDeux[substr($tableauTemporaire[$i], $j, 1)];
                        if (substr($tableauTemporaire[$i], $j, 1) == 1 || substr($tableauTemporaire[$i], $j, 1) == 7 || substr($tableauTemporaire[$i], $j, 1) == 9)
                            break;
                    case strlen($tableauTemporaire[$i]) - 1:// si on est dans les unites
                        if (substr($tableauTemporaire[$i], $j, 1) == 1)
                            echo " et ";
                        if (substr($tableauTemporaire[$i], $j - 1, 1) == 1 || substr($tableauTemporaire[$i], $j - 1, 1) == 7 || substr($tableauTemporaire[$i], $j - 1, 1) == 9)
                            if (substr($tableauTemporaire[$i], $j, 1) < 7)
                                echo "-" . $tableConversionDeLaDizaineQuiFaitChier[substr($tableauTemporaire[$i], $j, 1)];
                            else
                                echo "-" . $tableConversionEtapeUnOuTrois[substr($tableauTemporaire[$i], $j, 1)];
                        else
                            echo "-" . $tableConversionEtapeUnOuTrois[substr($tableauTemporaire[$i], $j, 1)];
                        break;
                }
            }
            echo " " . $tablePuissancesDeDix[count($tableauTemporaire) - $i - 1] . " "; // à quelle multiple de 10^3 on est ?
        }
    }
    
    public static function commStruc($s = 0) {
        $d = sprintf("%010s", $s);
        $modulo = (bcmod($s, 97) == 0 ? 97 : bcmod($s, 97));
        return sprintf("%s/%s/%s%02d", substr($d, 0, 3), substr($d, 3, 4), substr($d, 7, 3), $modulo);
    }
    
    public static function premierseptembre() {
        $ajd = time();
        $sept1 = strtotime("September 1st", $ajd);
        if ($ajd < $sept1) {
            $sept1 = strtotime("-1 Year", $sept1);
        }
        return date('Y-m-d', $sept1);
    }
    
    /**
     * Enlève tous les accents d'une chaîne de caractères
     * @param type $string
     * @return type string
     */
    public function sans_accents($string) {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }
    
    /**
     * Retourne un nom d'utilisateur tout propre à partir du prénom et du nom
     * @param string $prenom
     * @param string $nom
     * @return string
     */
     public function prenomme($prenom, $nom, $gabarit='long') {
        $sp = preg_replace('/[^A-Za-z0-9]/', '', strtolower($this->sans_accents($prenom)));
        $sn = preg_replace('/[^A-Za-z0-9]/', '', strtolower($this->sans_accents($nom)));
        if ($gabarit == 'long') {
            $username = substr($sp, 0, 12).".".substr($sn, 0, 12);
        } else {
            // court
            $username = substr($sp, 0, 1).".".substr($sn, 0, 12);
        }
        return $username;
    }

    
    public function mouvements_chenille($id) {
        $mauvais_mouvement = \App\Models\Mouvement::find($id);
        $pas = $mauvais_mouvement->montant;
        $user = $mauvais_mouvement->user;
        $chenille = $user->mouvements->where('id', '>', $id);
        echo "L'utilisateur ".$user->name." a un solde de ".$user->solde."\n";
        echo "Si je supprime le mouvement id $id de ".$pas.", les mouvements suivants seront décalés:\n";
        foreach ($chenille as $c) {
            echo "Le mouvement id ".$c->id." de ".$c->montant." a un solde de ".($c->solde - $pas)." au lieu de ".$c->solde."\n";
            $c->solde -= $pas;
            $c->save();
        }
        $mauvais_mouvement->delete();
        echo "Au final, le solde passe de ".$user->solde." à ".($user->solde - $pas)."\n";
        $user->solde -= $pas;
        $user->save();
    }
    
    /**
     * Copié collé d'une réponse StackOverflow de Peter Fox
     * @param type $iban
     * @return boolean
     */
    public static function checkIBAN($iban)
{
    $iban = strtolower(str_replace(' ','',$iban));
    $Countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
    $Chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);

    $pays = $Countries[substr($iban,0,2)] ?? 1000;
    if(strlen($iban) == $pays){

        $MovedChar = substr($iban, 4).substr($iban,0,4);
        $MovedCharArray = str_split($MovedChar);
        $NewString = "";

        foreach($MovedCharArray AS $key => $value){
            if(!is_numeric($MovedCharArray[$key])){
                $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
            }
            $NewString .= $MovedCharArray[$key];
        }

        if(bcmod($NewString, '97') == 1)
        {
            return true;
        }
    }
    return false;
}

}

