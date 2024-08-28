<?php

namespace oktaa\Database\Interfaces;

enum OrderByType: string
{
    case ASC = "ASC";
    case DESC = "DESC";
    case NULL = "";
}
