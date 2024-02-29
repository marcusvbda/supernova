<?php

namespace marcusvbda\supernova;

enum FILTER_TYPES: string
{
    case NUMBER = "number";
    case NUMBER_RANGE = "number_range";
    case TEXT = "text";
    case DATE = "date";
    case DATE_RANGE = "date_range";
    case SELECT = "select";
}
