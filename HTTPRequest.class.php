<?php

class HTTPRequest {
    private $response = null;
    private $responseMetadata = null;
    private $requestURL = null;
    private $requestParams = null;

    public function __construct( $_url, $_data = null, $_method = 'GET', $_contentType = null ) {
        $this->requestURL = $_url;
        
        if( !in_array( $_method, array( 'GET', 'POST', 'PUT', 'DELETE' ) ) )
          throw new Exception( 'Invalid HTTP request method. Valid methods are: GET, POST, PUT and DELETE' );

        $this->requestParams['http']['method'] = $_method;
        
        $this->requestParams['http']['header'] = '';
        
        if( $_contentType != null )
          $this->addHeader( 'Content-Type', $_contentType );
          
        if( $_data != null )
          $this->requestParams['http']['content'] = $_data;
    }
    
    public function addHeader( $_param, $_value ) {
      $this->requestParams['http']['header'] .= $_param.": ".$_value."\r\n";
    }
    
    public function process() {
    	$context = stream_context_create( $this->requestParams );
		  $fp = @fopen( $this->requestURL, 'rb', false, $context );
		  
		  if ( !$fp )
		    throw new Exception( 'Unable to open URL: '.$this->requestURL );

		  $this->response = @stream_get_contents( $fp );
		  
		  $this->responseMetadata = @stream_get_meta_data( $fp );
		  
		  if ( $this->response === false )
		    throw new Exception( 'Problem reading data from '.$this->requestURL );
		  
		  return $this->response;
    }
    
    public function getResponse() {
    	return $this->response;
    }
    
    public function getResponseCode() {
    	preg_match( '@(HTTP/[0-9\.]+)(\ )([0-9]+)(\ )([A-Z]+)@', $this->responseMetadata['wrapper_data']['0'], $matches );
    	return $matches[3];
    }
}
?>