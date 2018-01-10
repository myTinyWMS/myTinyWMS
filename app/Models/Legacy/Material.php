<?php

namespace Mss\Models\Legacy;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Mss\Model\ORM\Material
 *
 * @property integer $id
 * @property string $bestnr
 * @property integer $hersteller
 * @property string $artikelbezeichnung
 * @property integer $type
 * @property integer $bestand
 * @property integer $mindbestand
 * @property integer $verbrauch
 * @property integer $bestellzeitpunkt
 * @property integer $maschinenzugehoerigkeit
 * @property float $preis
 * @property integer $bestellmenge
 * @property string $bemerkung
 * @property string $gefahrenklasse
 * @property string $lieferzeit
 * @property integer $status
 * @property string $barcode
 * @property integer $entnahmemenge
 * @property string $barcode_file
 * @property integer $mail_to_supplier
 * @property integer $bestell_status
 * @property Carbon $date
 * @property integer $sort_id
 * @property boolean $inventur
 */
class Material extends Model {
    protected $table = "material";
    protected $connection = 'onp';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];
    protected $dates = ['date'];
    protected $dateFormat = 'Y-m-d H:i:s.u';
}