<?php

namespace App\Http\Controllers;

abstract class Controller
{
    const HTTP_OK = 201;
    const HTTP_UNAUTHORIZED = 403;
    const CREATED = "created successfully";
    const UPDATED = "updated successfully";
    const DELETED = "deleted successfully";
    const NOT_FOUND = "not found";
    const UNAUTHORIZED = "unauthorized";
    const  RETRIEVED = "retrieved successfully";
}
