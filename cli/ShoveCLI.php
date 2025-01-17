<?php
namespace Shove\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ShoveCLI extends Command
{

	/**
	 * @var OutputInterface $_output. Static output objecy
	 *
	 * @since 1.5.0
	 */
	static $_output;

	/**
	 * @var OutputInterface $output. Output object
	 *
	 * @since 1.5.0
	 */
	private $output;

	/**
	 * @var InputInterface $input. Input object
	 *
	 * @since 1.5.0
	 */
	private $input;

	/**
	 * @var array $theme_info. Theme info
	 *
	 * @since 1.5.0
	 */
	private $theme_info;

	/**
	 * @var array $paths. Utility paths for console commands.
	 *
	 * @since 1.5.0
	 */
	private $paths;

	/**
	 * @var array $messages. Default messages
	 *
	 * @since 1.5.0
	 */
	private $messages;

	/**
	 * @var array $prefixes. Utility prefixes.
	 *
	 * @since 1.5.0
	 */
	private $prefixes;

	/**
	 * @var array $prefixes. Utility prefixes.
	 *
	 * @since 1.5.0
	 */
	private $questionHelper;


	/**
	 * @var integer. Number of total tasks ran.
	 *
	 * @since 1.5.0
	 */
	private $total_tasks;

	/**
	 * @var integer. Number of total tasks ran successfully.
	 *
	 * @since 1.5.0
	 */
	private $success_tasks;

	/**
	 * Parent command constructor.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|void|null
	 *
	 * @since 1.5.0
	 */
    protected function execute( InputInterface $input, OutputInterface $output ) {

    	$this->set_output( $output );
    	$this->set_input( $input );
    	$this->set_theme_info();
    	$this->set_paths();
    	$this->set_default_messages();
    	$this->set_prefixes();
    	$this->set_helpers();
    	$this->set_tasks();

        $this->command_init();
        die();
    }

    // Use this method in extended classes
	protected function command_init() {
		$action = $this->input()->getArgument( $this->action );

		// check if exists a method for the action required
		if ( method_exists( get_class($this), $action ) ) {
			$this->$action();
		}
	}

    /*
     *  ---------------------------------------------------------------------------------------------
     *                                          SETTER
     *  ---------------------------------------------------------------------------------------------
     */


	/**
	 * Set output object
	 *
	 * @param OutputInterface $output
	 *
	 * @since 1.5.0
	 */
    public function set_output( OutputInterface $output ) {
		self::$_output = $output;
		$this->output  = $output;
	}

	/**
	 * Set input object
	 *
	 * @param InputInterface $input
	 *
	 * @since 1.5.0
	 */
	public function set_input( InputInterface $input ) {
		$this->input = $input;
	}

	/**
	 * Set active theme info
	 *
	 * @since 1.5.0
	 */
    public function set_theme_info(){
		$style_file       = get_template_directory() . '/style.css';
		$file_headers = array(
			'Name'        => 'Theme Name',
			'ThemeURI'    => 'Theme URI',
			'Description' => 'Description',
			'Author'      => 'Author',
			'AuthorURI'   => 'Author URI',
			'Version'     => 'Version',
			'Template'    => 'Template',
			'Status'      => 'Status',
			'Tags'        => 'Tags',
			'TextDomain'  => 'Text Domain',
			'DomainPath'  => 'Domain Path'
		);
		$this->theme_info = FileManager::get_file_data($style_file, $file_headers);
	}


	/**
	 * Set utility paths.
	 *
	 * @since 1.5.0
	 */
	public function set_paths() {

		$plugin_path = str_replace('/cli', '', __DIR__ );
		$theme_path = get_template_directory() ;

		if ( is_dir( $theme_path . '/resources') ) {
			$theme_path = $theme_path . '/resources';
		}

		$this->paths = [
			'templates'  => $this->add_slash( __DIR__. '/templates' ),
			'theme'      => $this->add_slash( $theme_path ),
			'plugin'     => $this->add_slash( $plugin_path ),

			'stubs'            => $this->add_slash(  __DIR__. '/stubs' ),
			'stubs.block'      => $this->add_slash( __DIR__. '/stubs/block' ),
			'stubs.component'  => $this->add_slash( __DIR__. '/stubs/component' ),
			'stubs.module'     => $this->add_slash( __DIR__. '/stubs/module' ),
			'stubs.plugin'     => $this->add_slash( __DIR__. '/stubs/acf-gutenberg' ),
			'stubs.block.scss' => __DIR__. '/stubs/blocks.scss',

			'plugin.blocks'     => $this->add_slash( $plugin_path . '/resources/blocks' ),
			'plugin.components' => $this->add_slash( $plugin_path . '/resources/components' ),
			'plugin.modules'    => false,

			'theme.plugin'     => $this->add_slash( $theme_path . '/acf-gutenberg/' ),
			'theme.config'     => $this->add_slash( $theme_path . '/acf-gutenberg/config' ),
			'theme.blocks'     => $this->add_slash( $theme_path . '/acf-gutenberg/blocks' ),
			'theme.components' => $this->add_slash( $theme_path . '/acf-gutenberg/components' ),
			'theme.modules'    => $this->add_slash( $theme_path . '/acf-gutenberg/modules' ),
			'theme.assets'     => $this->add_slash( $theme_path . '/assets/' ),
		];
	}

	/**
	 * Set default messages.
	 *
	 * @since 1.5.0
	 */
	public function set_default_messages() {
		$this->messages = [
			'tasks_ready'     => "------ All task ready ------",
			'action_canceled' => "Action canceled",
		];
	}

	/**
	 * Set prefixes.
	 *
	 * @since 1.5.0
	 */
	public function set_prefixes() {
		$this->prefixes = [
			'css' => "b-",
		];
	}

	/**
	 * Set helpers.
	 *
	 * @since 1.5.0
	 */
	public function set_helpers() {
		$this->questionHelper = $this->getHelper('question');
	}

	/**
	 * Set tasks counters.
	 *
	 * @since 1.5.0
	 */
	public function set_tasks() {
		$this->total_tasks   = (int) 0;
		$this->success_tasks = (int) 0;
	}





	/*
     *  ---------------------------------------------------------------------------------------------
     *                                          GETTER
     *  ---------------------------------------------------------------------------------------------
     */


	/**
	 * Get output object
	 *
	 * @return OutputInterface
	 *
	 * @since 1.5.0
	 */
	public function output() {
		return $this->output;
	}

	/**
	 * Get input object
	 *
	 * @return InputInterface
	 *
	 * @since 1.5.0
	 */
	public function input() {
		return $this->input;
	}

	/**
	 * Get input option given
	 *
	 * @param string $option. Option slug
	 * @return bool|string|string[]|null
	 *
	 * @since 1.5.0
	 */
	public function option( $option ) {
		return $this->input()->getOption( $option );
	}

	/**
	 * Get input argument given
	 *
	 * @param string $argument. Argument slug
	 * @return bool|string|string[]|null
	 *
	 * @since 1.5.0
	 */
	public function argument( $argument ) {
		return $this->input()->getArgument( $argument );
	}

	/**
	 * Get paths list
	 *
	 * @return object
	 *
	 * @since 1.5.0
	 */
	public function paths() {
		return (object) $this->paths;
	}

	/**
	 * Get defined path
	 *
	 * @param string $path. Path slug
	 * @return bool|mixed
	 *
	 * @since 1.5.0
	 */
	public function path( $path ) {
		return ( isset( $this->paths[$path] ) ) ? $this->paths[$path] : false;
	}

	/**
	 * Get default messages list
	 *
	 * @return object
	 *
	 * @since 1.5.0
	 */
	public function get_messages() {
		return (object) $this->messages;
	}

	/**
	 * Get defined message
	 *
	 * @param string $message. Message slug
	 * @return bool|mixed
	 *
	 * @since 1.5.0
	 */
	public function get_message( $message ) {
		return ( isset( $this->messages[$message] ) ) ? $this->messages[$message] : false;
	}

	/**
	 * Get prefix list
	 *
	 * @return object
	 *
	 * @since 1.5.0
	 */
	public function prefixes() {
		return (object) $this->prefixes;
	}

	/**
	 * Get defined prefix
	 *
	 * @param string $prefix. Prefix slug
	 * @return bool|mixed
	 *
	 * @since 1.5.0
	 */
	public function prefix( $prefix ) {
		return ( isset( $this->prefixes[$prefix] ) ) ? $this->prefixes[$prefix] : false;
	}

	/**
	 * Get question helper
	 *
	 * @return bool|mixed
	 *
	 * @since 1.5.0
	 */
	public function question() {
		return $this->questionHelper;
	}

	/**
	 * Increment the number of $total_task
	 *
	 * @param boolean $successfully. Specify if the task was successful. Default: True.
	 *
	 * @since 1.5.0
	 */
	public function add_task( $successfully = true ) {
		if ( $successfully ) $this->success_tasks++;

		$this->total_tasks++;
	}

	/**
	 * Print the tasks resume
	 *
	 * @since 1.5.0
	 */
	public function task_resume() {
		if ( $this->success_tasks == $this->total_tasks ){
			ShovePrint::info("All tasks were successfully. {$this->success_tasks}/{$this->total_tasks}");
		} else {
			ShovePrint::message("Success tasks: <info>{$this->success_tasks}</info>/{$this->total_tasks}");
		}
	}


	/*
     *  ---------------------------------------------------------------------------------------------
     *                                          UTILITIES
     *  ---------------------------------------------------------------------------------------------
     */

	public function add_slash($path){
		if (substr($path, -1) != "/" ){
			$path.= '/';
		}
		return $path;
	}


	public function ask_yes_no( $message = false ) {
		$message  = ( ! $message ) ? 'Continue with this action? (y/n) ' : $message;
		$question = new ConfirmationQuestion($message, false);
		return  $this->question()->ask($this->input(), $this->output(), $question);
	}

	public function response_yes( $response ) {
		return ( $response == 'y' || $response == "yes") ? true : false;
	}

















	/*
     *  ---------------------------------------------------------------------------------------------
     *                                          TO REFACTOR
     *  ---------------------------------------------------------------------------------------------
     */


	public function set_name_by_prefix($block, $prefix){
		$new_block_name = $block;
		if ($prefix){
			$new_block_name = $prefix."-".$block;
		}
		return $new_block_name;
	}

	public function initial_setting(){
		if (isset($this->commandArgumentName)){
			$name = $this->input->getArgument($this->commandArgumentName);
		}else if (isset($this->commandArgumentPrefix)){
			$name = $this->input->getArgument($this->commandArgumentPrefix)."-".$this->input->getArgument($this->commandArgumentBlock);
		}else{
			$name = false;
		}
		if (isset($this->commandArgumentBlock)){
			$block = $this->input->getArgument($this->commandArgumentBlock);
			$prefix = false;
			if (isset($this->commandArgumentPrefix)){
				$prefix = $this->input->getArgument($this->commandArgumentPrefix);
			}
			$name = $this->set_name_by_prefix($block, $prefix);
		}
		$this->set_block_labels($name);
	}




	public function import_block_cli_file($blocks_dir){
		if (strpos($blocks_dir, 'resources')){
			$blocks_dir = str_replace('resources', '', $blocks_dir);
		}
		$error = $this->fileManager()->copy_file(
			$this->block_cli_file,
			$blocks_dir,
			"block",
			'import block cli file'
		);
		if ($error){
			$this->print($error, 'error');
		}else{
			$this->print(
				" ✓ Block CLI file imported in theme directory",
				'info');
		}

	}

    public function is_active_theme(){
        $is_active_theme = true;
        $active_theme = $this->get_theme_root($this->theme_blocks_dir);
        $bash_dir = getcwd();
        $active_theme = $this->add_slash($active_theme);
        $bash_dir = $this->add_slash($bash_dir);
        if ($bash_dir != $active_theme){
            $is_active_theme = false;
        }
//        $this->print($active_theme);
//        $this->print($bash_dir);

        return $is_active_theme;
    }












}
