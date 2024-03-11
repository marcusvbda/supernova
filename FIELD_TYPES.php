<?php

namespace marcusvbda\supernova;

enum FIELD_TYPES: string
{
    case COLOR = "color";
    case URL = "url";
    case TEXT = "text";
    case TEXTAREA = "textarea";
    case PASSWORD = "password";
    case NUMBER = "number";
    case SELECT = "select";
    case MODULE = "module";
    case UPLOAD = "upload";
}
