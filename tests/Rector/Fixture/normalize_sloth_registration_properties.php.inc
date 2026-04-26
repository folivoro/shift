<?php

class Page extends Model
{
    protected array $options = ['public' => true];
}
-----
<?php

class Page extends Model
{
    public static array $options = ['public' => true];
}