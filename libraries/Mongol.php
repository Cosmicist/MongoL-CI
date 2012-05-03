<?php

/**
 * Mongol - Simple MongoDB micro-wrapper library for CodeIgniter
 * 
 * @version 1.0.0
 * @author Luciano Longo <luciano.longo@gmail.com>
 * @copyright Copyright (c), Luciano Longo
 * @license http://opensource.org/licenses/MIT MIT License
 * @link http://hg.longo.me/mongol
 */
class Mongol extends Mongo
{
    protected $dbname;
    protected $group;
    protected $config;
    protected $dsn;
    
    public function __construct( $group = NULL )
    {
        // Build dsn
        $this->_buildDSN( $group );
        
        // Connect!
        parent::__construct( $this->dsn );
        
        // Get CI instance
        $CI = get_instance();
        
        // Create a "shortcut" on the CI instance with the group name if available
        if( ! $CI->{$this->group} )
            $CI->{$this->group} = $this->{$this->dbname};
        
        // Create another shortcut with a generic name "mdb"
        $CI->mdb = $this->{$this->dbname};
    }
    
    private function _buildDSN( $group = NULL )
    {
        // If $group does not look like a dsn, try to use it as a config group
        if( ! preg_match('/^mongodb:\/\//i', $group) )
        {
            // Load config
            include_once APPPATH.'config/mongodb.php';
            
            // Save config
            $this->config = $mdb;
            
            $this->group = $group = $group ? $group : $mdb['default_group'];
            
            // Select config
            if( ! ( $c = @$mdb[$group] ) )
                throw new MongoConnectionException("There is no config group named '$group' on your application's config/mongodb.php!");
            
            // Check for auth data
            if( $c['user'] && $c['pass'] )
                $auth = "{$c['user']}:{$c['pass']}@";
            
            // Check for non-default port
            if( is_int( $c['port'] ) && $c['port'] != 27017 )
                $port = ":{$c['port']}";
            
            // Save the DB name apart
            $this->dbname = $c['name'];
            
            $this->dsn = "mongodb://$auth{$c['host']}$port/{$c['name']}";
        }
        else
            $this->dsn = $group;
        
        return $this->dsn;
    }
    
    /**
     * Creates a new connection to a DB using the given group or DSN
     * 
     * @param type $group The config group name or connection DSN
     * @return \self 
     */
    public static function use_db( $group )
    {
        return new self( $group );
    }
}
