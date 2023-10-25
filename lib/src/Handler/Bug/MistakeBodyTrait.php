<?php

namespace Ilustro\Handler\Bug;

trait MistakeBodyTrait {

    /**
     * @var string
     */
    protected $htitle = 'Mistake <~(é_é)';

    /**
     * @var string
     */
    protected $btitle;

    /**
     * @var array
     */
    protected $table_msg = [];

    /**
     * @var array
     */
    protected $menu_items= [];

    /**
     * @param string $title
     */
    public function setHeadTitle($title)
    {
        $this->htitle = $title;
    }

    /**
     * @param string $title
     */
    public function setBodyTitle($title)
    {
        $this->btitle = $title;
    }

    /**
     * @param string $title
     * @param string $icon
     * @param string $referer referencia del contenido a mostar por el atributo [data-content]
     */
    public function setMenuItem($title, $icon, $referer  = '')
    {
        $this->menu_items[] = [$title, constant('PATH_ICON') . $icon, $referer];
    }

    /**
     * @param string $cell
     * @param string $value
     */
    public function addMessageTable($cell, $value)
    {
        $this->table_msg[] = [$cell, $value];
    }
}