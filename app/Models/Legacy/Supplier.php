<?php

namespace Mss\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

/**
 * Mss\Model\ORM\Categoriy
 *
 * @property integer $id
 * @property string $company_name
 * @property string $email
 * @property string $phone
 * @property string $contact_person
 * @property integer $category_id
 * @property string $text_for_mail
 * @property string $website
 * @property string $comment
 */
class Supplier extends Model {
    protected $table = "material_supplier";
    protected $connection = 'onp';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];
}