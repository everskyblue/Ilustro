<?php

namespace Kowo\Ilustro\Handler\Bug;


use Kowo\Ilustro\Html\Tag;
use Kowo\Ilustro\Html\AppendNode;


trait MistakeRenderTrait {

    public function initializeContent()
    {

        $this->html = Tag::html()->insertChild(
            Tag::meta(Tag::NOT_CLOSE, [
                'charset' => 'UTF-8'
            ])
        )/*->insertChild(
            Tag::meta(Tag::NOT_CLOSE, [
                'name' => 'viewport',
                'content' => 'width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'
            ])
        )*/;

        $this->head = Tag::head()
                        ->generate(2, [['title', $this->htitle], ['style', file_get_contents(__DIR__.'/mistake.style.css')]])
                        ->insideAll();

        $this->body = Tag::body()
                        ->generate(3, ['header', 'main', ['footer', $this->footer()]])
                        ->insideAll();

        $this->container = Tag::div(Tag::attr(['class' => 'ms-container']));
        $this->content = Tag::div(Tag::attr(['class' => 'ms-content-wrapper']));
    }

    /**
     * @var array
     */
    protected $nav_menu_info = [
        'request' => 'server',
        'storage-data' => 'session & cookie',
        'env' =>'environment'
    ];

    protected function configMenu()
    {
        $this->setMenuItem('mensaje', 'ic_message.png', 'msg');
        $this->setMenuItem('mas informacion', 'ic_info.png', 'info');
        $this->setMenuItem('errores', 'ic_bug_report.png', 'bugs');
        $this->setMenuItem('configuraciones', 'ic_perm_data_setting.png', 'cfg');
    }

    /**
     * @return object
     */
    protected function sectionMsg()
    {
        $section = Tag::section(Tag::attr([
        	'class' => 'ms-content msg-error-app',
            'data-content' => 'msg'
        ]))->generate(2, [
        	['div', $this->btitle],
        	['div', Tag::attr(['class' => 'ms-table'])]
        ])->insideAll();

        $items = $section->generate(count($this->table_msg), 'div', Tag::attr([
        	'class' => 'ms-cell'
        ]))->getNodeGen();

        foreach ($items as $index => $item) {
            $mi = $this->table_msg[$index];
            $items[$index]['body'][] = Tag::div($mi[0], ['class'=>'ms-row'])->getNode()[0];
            $items[$index]['body'][] = Tag::div($mi[1], ['class'=>'ms-row'])->getNode()[0];
        }

        $section->resetNodeGen();

        $apn = new AppendNode($section, 'tag:div', 'class:ms-table');
        $apn->push($items);

        return ((object) [
            'element' => $section,
            'append_node' => $apn
        ]);
    }

    protected function sectionMoreInfo()
    {
        $nav = Tag::nav(['class' => 'ms-nav-info']);

        foreach($this->nav_menu_info as $referer_content => $text) {
            $nav->insertChild(Tag::span($text, [
                'data-referer' => $referer_content,
                'onclick' => 'mistake.showInfoContent(this)'
            ]));
        }

        $server = $this->createTable($_SERVER);
        $cookie = $this->createTable($_COOKIE);
        $session = $this->createTable(session_status() === PHP_SESSION_NONE ? [] : $_SESSION);
        $env = $this->createTable($_ENV);

        $cser = $this->blockElement('request','ms-block-show')->insertChild($server);
        $csc = $this->blockElement('storage-data')->insertChild($cookie)->insertChild($session);
        $cenv = $this->blockElement('env')->insertChild($env);

        $this->content->insertChild(
            Tag::section([
                'class' => 'ms-content ms-info-content',
                'data-content' => 'info'
            ])->insertChild($nav)
                ->insertChild($cser)
                ->insertChild($csc)
                ->insertChild($cenv)
        );
    }

    /**
     * @param array $arr
     * @return Kowo\Ilustro\Html\TreeElement
     */
    protected function createTable(array $arr)
    {
        $ds = Tag::div(Tag::attr(['class' => 'ms-table']));

        foreach($arr as $key => $val) {
            $ds->insertChild(
                Tag::div(['class' => 'ms-item'])
                ->insertChild(Tag::div($key))
                ->insertChild(Tag::div($val))
            );
        }

        if (count($ds->getNode()[0]['body'])===0){
            $ds->insertChild(Tag::p('no hay informacion', ['style' => 'font-size:22px;color:red']));
        }

        return Tag::div(['class' => 'ms-content-table'])->insertChild($ds);
    }

    /**
     * @param string $data
     * @param string $class_name
     * @return Kowo\Ilustro\Html\TreeElement
     */
    protected function blockElement($data, $class_name = '')
    {
        return Tag::div([
            'class' => 'ms-block'. (strlen($class_name)>0?(' '. $class_name) : ''),
            'data-content' => $data
        ]);
    }

    /**
     * @param string $file
     * @param int $errline
     * @return object
     */
    protected function createSourceContent($file, $errline)
    {
        $text = join("", array_filter(preg_split("/[\r]/", file_get_contents($file))));

        $source_content = Tag::div(['class' => 'source-container'])
            ->insertChild(Tag::div($file.':'.$errline, ['class' => 'file-error']));

        $wcode = Tag::div(['class' => 'code-wrapper'])->generate(2, [
            ['div', Tag::attr(['class' => 'code-lines'])],
            ['pre', highlight_string($text, true), Tag::attr(['class' => 'code-source'])]
        ])->insideAll();

        $lines = explode("\n", $text);

        $apn = new AppendNode($wcode, 'attr:class@code-lines');

        foreach($lines as $line => $code) {
            $attr = ['class'=>'lino'];
            if(($line+1) == $errline)
                $attr['data-scroll'] = $errline;
            $block = Tag::div()
                ->insertChild(Tag::span((string)($line+1), $attr));
            $apn->push($block->getNode());
        }

        $source_content->insertChild($wcode);

        return ((object) [
            'element' => $source_content,
            'append_node' => $apn
        ]);
    }

    protected function footer()
    {
        function convert($size){
            $unit=array('b','kb','mb','gb','tb','pb');
            return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
        }

        $footer ='<div><p>
            <img width="24px" src="'. constant('PATH_ICON') .'ic_memory.png"> '. convert(memory_get_usage(true)).
        '</p></div>';
        $footer .= '<div> php v-'.PHP_VERSION.'</div>';
        return $footer;
    }

    /**
     * @param array $files
     */
    protected function sectionStack(array $files)
    {
        $section = Tag::section([
            'class' => 'ms-content bugs',
            'data-content' => 'bugs'
        ]);

        foreach($files as $file) {
            $exp = explode(':', $file);
            $div = Tag::div()->insertChild(Tag::p( preg_replace('/\#(\d)?/i', '<span class="bn">$1</span>', $exp[0]) ));
             if (isset($exp[1]))
                 $div->insertChild(Tag::p($exp[1], ['class' => 'bug-invk']));
            $section->insertChild($div);
        }

        $this->content->insertChild($section);
    }

    /**
     * @param string $htitle
     * @param string $msg
     * @param int $code
     */
    protected function message($btitle, $msg, $code)
    {
        $this->setBodyTitle($btitle);
        $this->addMessageTable('mensaje', $msg);
        $this->addMessageTable('codigo', (string)$code);
        $this->addMessageTable('metodo', $_SERVER['REQUEST_METHOD']);
        $this->addMessageTable('url', $_SERVER['REQUEST_URI']);
    }

    protected function structureHTML()
    {
        $this->createMenu();
        $this->html->insertChild($this->head)->insertChild($this->body);
        $b=new AppendNode($this->html, 'tag:main');
        $b->push($this->container->getNode());
        $b->query('tag:body')->push(Tag::script('var menus = '. json_encode($this->menu_items).";\n". file_get_contents(__DIR__.'/mistake.js'))->getNode());
    }

    protected function createMenu()
    {
        $menu = Tag::nav(Tag::attr([
            'class' => 'ms-nav-menu'
        ]))->generate(count($this->menu_items), 'div', Tag::attr([
            'class' => 'ms-menu-item'
        ]))->insideAll();

        $append = Tag::structure($menu)->span->img(TAG::NOT_CLOSE, Tag::attr([
            'async' => true
        ]))->append('attr:class@ms-menu-item');
        $append->setTreeElement($this->body);
        $append->query('tag:header')->push($menu->getNode());
    }

    /**
     * @param string $fild
     * @param string $line
     * @param string $stack
     */
    protected function output($file, $line, $stack)
    {
        $o_source = $this->createSourceContent($file, $line);

        $section = $this->sectionMsg();

        $section->element->insertChild($o_source->element);

        $this->content->insertChild($section->element);

        $this->sectionMoreInfo();

        $this->sectionStack(explode("\n", $stack));

        $this->container->insertChild($this->content);

        $this->structureHTML();

        http_response_code(500);

        die((Tag::render($this->html)));
    }
}