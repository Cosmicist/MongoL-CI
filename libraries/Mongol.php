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
    
    public function __construct( $group = NULL, $disable_shortcut = FALSE )
    {
        // Build dsn
        $this->_buildDSN( $group );
        
        // Connect!
        parent::__construct( $this->dsn );
        
        // Create the shortcuts only if the're enabled
        if( ! $disable_shortcut )
        {
            // Get CI instance
            $CI = get_instance();

            // Create a "shortcut" on the CI instance with the group/db name if available
            $dbns = $this->group ? $this->group : $this->dbname;
            if( ! $CI->{$dbns} )
                $CI->{$dbns} = $this->{$this->dbname};

            // Create another shortcut with a generic name "mdb"
            if( ! $group )
                $CI->mdb = $this->{$this->dbname};
        }
    }
    
    public function get_db()
    {
        return $this->{$this->dbname};
    }
    
    private function _buildDSN( $group = NULL )
    {
        // If $group does not look like a dsn, try to use it as a config group
        if( ! preg_match('/^mongodb:\/\//i', $group) )
        {
            $mdb = $this->config;
            
            // Load config if it wasn't already loaded
            if( ! $mdb )
            {
                include APPPATH.'config/mongodb.php';

                // Save config
                $this->config = $mdb;
            }
            
            $this->group = $group = $group ? $group : $mdb['default_group'];
            
            // Select config
            if( ! ( $c = $mdb[$group] ) )
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
        {
            $this->dsn = $group;
            
            // Try to get the DB name
            if( preg_match('/\/([a-z0-9\-_]+)$/i', $group, $m) )
                $this->dbname = $m[1];
        }
        
        return $this->dsn;
    }
    
    /**
     * Creates a new connection to a DB using the given group or DSN
     * 
     * @param type $group The config group name or connection DSN
     * @return Mongol
     */
    public static function use_db( $group, $disable_shortcut = FALSE )
    {
        return new self( $group, $disable_shortcut );
    }
}
