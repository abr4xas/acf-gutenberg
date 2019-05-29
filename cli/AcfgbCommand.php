<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AcfgbCommand extends Command
{

    protected $css_class_prefix = "b-";
    protected $blocks_templates_dir = __DIR__."/_base/blocks_templates";
    protected $block_base_dir = __DIR__."/_base/_block-base";
    protected $js_base_file = __DIR__."/_base/route.js";
    protected $plugin_blocks_dir = __DIR__ .'/../resources/blocks/';
    protected $theme_blocks_dir = '';
    protected $block_labels = [];
    protected $target;
    protected $output;
    protected $input;
    protected $fileManager;

    protected function execute(InputInterface $input, OutputInterface $output){
        $this->output = $output;
        $this->input = $input;
        $this->input = $input;
        $this->fileManager = new FileManager();
        $this->set_target($this->input);
        if (isset($this->commandArgumentName)){
            $this->set_block_labels($this->input->getArgument($this->commandArgumentName));
        }
        $this->command_init();
    }

    protected function command_init(){
        // Use this method in extended classes
    }


    /*
     *  ---------------------------------------------------------------------------------------------
     *                                          SETTER
     *  ---------------------------------------------------------------------------------------------
     */

    public function set_target($input){
        $this->target = $this->get_target($input);
    }

    public function set_block_labels($name){
        $this->block_labels = (object) [
          'name' => $name,
          'slug' => $this->name_to_slug($name),
          'title' => $this->name_to_title($name),
          'css_class' => $this->name_to_css_class($name),
          'php_class' => $this->name_to_php_class($name),
          'scss_file' => $this->slug_to_css_file(
              $this->name_to_slug($name)
          ),
          'js_file' => $this->slug_to_js_file(
              $this->name_to_slug($name)
          ),
        ];
    }

    public function fileManager(){
        return $this->fileManager;
    }


    /*
     *  ---------------------------------------------------------------------------------------------
     *                                          TASKS
     *  ---------------------------------------------------------------------------------------------
     */


    public function rename_block_php_class($file, $php_class, $title = false){
        $error = $this->fileManager()->edit_file(
            'replace',
            $file,
            [
                'BlockBaseTitle' => $title,
                'BlockBase'      => $php_class,
             ],
            'rename php class'
        );
        if ($error){
            $this->output->writeln($error);
            die();
        }else{
            $this->output->writeln(" - PHP Class was replaced");
            $this->output->writeln(" - PHP Title was replaced");
        }
    }

    public function rename_block_css_class($file, $css_class){

        $error = $this->fileManager()->edit_file(
            'replace',
            $file,
            [
                'b-block-base' => $css_class,
            ],
            'rename css class'
        );

        if ($error){
            $this->output->writeln($error);
            die();
        }else{
            $this->output->writeln(" - CSS class was replaced");
        }
    }



    public function import_block_base($blocks_dir){
        $error = $this->fileManager()->copy_dir(
            $this->block_base_dir,
            $blocks_dir,
            $this->block_labels->slug,
            'import block base'
        );

        $output = ($error) ? $error : " - New block created in {$this->target} folder";
        $this->output->writeln($output);
    }


    public function import_js($blocks_dir){
        $error = $this->fileManager()->copy_file(
            $this->js_base_file,
            $blocks_dir.$this->block_labels->slug.'/',
            $this->block_labels->js_file.".js",
            'import js file base'
        );
        if ($error){
            $this->output->writeln($error);
        }else{
            $this->output->writeln(" - Js file base created in {$this->target} folder");
            $this->output->writeln(" - REMEMBER add this file to you JS routes file, like: ");
            $this->output->writeln("   --> import {$this->block_labels->js_file} from '../../acf-gutenberg/blocks/{$this->block_labels->slug}/{$this->block_labels->js_file}';");
        }

    }


    public function add_block_styles_to_blocks_scss($block_scss_file){
        $blocks_scss_file = $this->get_target_path().'../../assets/styles/blocks.scss';;

        $error = $this->fileManager()->edit_file(
            'add_to_bottom',
            $blocks_scss_file,
            [
                '@import "/../../acf-gutenberg/blocks/'.$this->block_labels->slug."/".$this->block_labels->scss_file.'";'
            ],
            'import block css in blocks.scss'
        );

        if ($error){
            $this->output->writeln($error);
            die();
        }else{
            $this->output->writeln(" - Block css was imported in blocks.scss");
        }

    }

    public function block_exist($slug){
        $block_exist = false;
        if (is_dir("$this->plugin_blocks_dir/$slug") || is_dir("$this->theme_blocks_dir/$slug")) {
            $block_exist = true;
        }
        return $block_exist;
    }

    public function block_template_exist($slug){
        $template_exist = false;
        if (is_dir("$this->blocks_templates_dir/$slug") || is_dir("$this->blocks_templates_dir/acfgb-$slug")) {
            $template_exist = true;
        }
        return $template_exist;
    }


    public function name_to_slug($str){
        $str = str_replace('_', '-', $str);
        $str = str_replace(' ', '-', $str);
        $str = strtolower($str);
        return $str;
    }

    public function name_to_php_class($str)
    {
        $str = ucwords(str_replace('-', ' ', $str));
        $str = ucwords(str_replace('_', ' ', $str));
        return str_replace(' ', '', $str);
    }

    public function name_to_css_class($str)
    {
        $str = ucwords(str_replace('_', '-', $str));
        $str = ucwords(str_replace(' ', '-', $str));
        $str = strtolower($str);
        return $this->css_class_prefix.$str;
    }

    public function name_to_title($str)
    {
        $str = str_replace('_', ' ', $str);
        $str = str_replace('-', ' ', $str);
        $str = ucfirst(strtolower($str));
        return $str;
    }

    public function slug_to_css_file($str)
    {
        $str = ucwords(str_replace('-', '_', $str));
        $str = ucwords(str_replace(' ', '_', $str));
        $str = strtolower($str);
        return $str;
    }

    public function slug_to_js_file($str)
    {
        $str_temp = explode('-', $str);
        $str = '';
        foreach ($str_temp as $word){
            $str.= ucfirst($word);
        }
        return $str;
    }

    /*
     *  ---------------------------------------------------------------------------------------------
     *                                          GETTER
     *  ---------------------------------------------------------------------------------------------
     */


    public function get_target(InputInterface $input){
        $target = 'theme';
        if ($input->getOption($this->optionTarget) && $input->getOption($this->optionTarget) == 'plugin') {
            $target = 'plugin';
        }
        return $target;
    }

    public function get_target_path(){
        $path = $this->theme_blocks_dir;
        if ($this->target == 'plugin') {
            $path = $this->plugin_blocks_dir;
        }
        return $path;
    }


    public function get_blocks($target = false){
        $blocks_paths = [
            'plugin' => $this->plugin_blocks_dir,
            'theme' => $this->theme_blocks_dir,
        ];
        if ($target){
            foreach ($blocks_paths as $key => $path){
                if ($key != $target){
                    unset($blocks_paths[$key]);
                }
            }
        }

        $blocks_list = array();
        if (is_array($blocks_paths)) {
            foreach ($blocks_paths as $key => $path) {
                $blocks_list[] = " ->Blocks in ".$key;
                if (is_dir($path)) {
                    $blocks = array_diff(scandir($path), ['..', '.']);
                    foreach ($blocks as $block_slug) {
                        $blocks_list[] = $block_slug;
                    }
                }
            }
        }
        return $blocks_list;
    }

    public function get_blocks_templates(){
        $blocks_path = $this->blocks_templates_dir;
        $blocks = array_diff(scandir($blocks_path), ['..', '.']);

        $blocks_list = array();
        foreach ($blocks as $block_slug) {
            $blocks_list[] = $block_slug;
        }
        return $blocks_list;
    }

    public function get_block_details(){
        return 'Block Details: '.$this->block_labels->title.' | class: '.$this->block_labels->php_class.' | slug: '.$this->block_labels->slug.' | css class: '.$this->block_labels->css_class;
    }


}