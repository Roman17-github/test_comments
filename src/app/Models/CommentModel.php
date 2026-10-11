<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'text',
        'date',
    ];

    protected $useTimestamps = false;
}
