<?php

namespace khessels\cms\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseFormat
{
    private function default( $request, $response, $passthrough = null){
        // transform response to formatted output
        if( $response->getStatusCode() >= 200 && $response->getStatusCode() < 400){
            $contentType = explode( ';', $response->headers->get('Content-Type'))[0];
            if( in_array($contentType, [ 'text/html', 'text/plain'])){
                $data = $response->getContent();
                $obj = ['data' => $data, 'data-type' => $contentType, 'status' => $response->getStatusCode()];
                if( ! empty( $passthrough)){
                    if( in_array( $request->headers->get('Content-Type'), ['text/plain', 'text/html'])){
                        $obj['passthrough']['data'] = $request->getContent();
                    }else{
                        $obj['passthrough']['data'] = $request->all();
                    }
                    $obj['passthrough']['data-type'] = $request->headers->get('Content-Type');
                    $obj['passthrough']['headers'] = $request->headers->all();
                }
                $response->setContent( $obj);
                return $response;
            }
            if( in_array($contentType, [ 'application/json', 'application/xml'])){
                $data = $response->getdata();
                $obj = ['data' => $data, 'data-type' => $contentType, 'status' => $response->getStatusCode()];
                if( ! empty( $passthrough)){
                    if( in_array( $request->headers->get('Content-Type'), ['text/plain', 'text/html'])){
                        $obj['passthrough']['data'] = $request->getContent();
                    }else{
                        $obj['passthrough']['data'] = $request->all();
                    }
                    $obj['passthrough']['data-type'] = $request->headers->get('Content-Type');
                    $obj['passthrough']['headers'] = $request->headers->all();
                }
                $response->setData( $obj);
                return $response;
            }
            return $response;
        }
        if( $response->getStatusCode() >= 400 && $response->getStatusCode() < 500){
            $contentType = explode( ';', $response->headers->get('Content-Type'))[0];
            if( in_array($contentType, [ 'text/xml', 'text/html', 'text/plain'])){
                $data = $response->getContent();
                if( ! empty( $passthrough)){
                    if( in_array( $request->headers->get('Content-Type'), ['text/plain', 'text/html'])){
                        $obj['passthrough']['data'] = $request->getContent();
                    }else{
                        $obj['passthrough']['data'] = $request->all();
                    }
                    $obj['passthrough']['data-type'] = $request->headers->get('Content-Type');
                    $obj['passthrough']['headers'] = $request->headers->all();
                }
                $obj = ['data' => $data, 'data-type' => $contentType, 'status' => $response->getStatusCode()];
                $response->setContent( $obj);
                return $response;
            }
            if( in_array($contentType, [ 'application/json', 'application/xml'])){
                $data = $response->getdata();
                if( ! empty( $data->error_message)){
                    $obj['error_message'] = $data->error_message;
                    unset( $data->error_message);
                }
                if( ! empty( $data->error_code)){
                    $obj['error_code'] = $data->error_code;
                    unset( $data->error_code);
                }
                if( ! empty( $passthrough)){
                    if( in_array( $request->headers->get('Content-Type'), ['text/plain', 'text/html'])){
                        $obj['passthrough']['data'] = $request->getContent();
                    }else{
                        $obj['passthrough']['data'] = $request->all();
                    }
                    $obj['passthrough']['data-type'] = $request->headers->get('Content-Type');
                    $obj['passthrough']['headers'] = $request->headers->all();
                }
                $obj['data'] = $data;
                $obj['status'] = $response->getStatusCode();
                $obj['data-type'] = $contentType;
                $response->setData( $obj);
                return $response;
            }
            return $response;

        }
        if( $response->getStatusCode() >= 500){
            $contentType = explode( ';', $response->headers->get('Content-Type'))[0];
            if( in_array($contentType, [ 'text/xml', 'text/html', 'text/plain'])){
                $obj = ['error_message' => 'Server error', 'status' => $response->getStatusCode()];
                $response->setContent( $obj);
                return $response;
            }
            if( in_array($contentType, [ 'application/json', 'application/xml'])){
                $obj = ['error_message' => 'Server error', 'status' => $response->getStatusCode()];
                $response->setData( $obj);
                return $response;
            }
            return $response;
        }
        return $response;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $format = 'default', $passthrough = null): Response
    {
        //return $next( $request);
        $format = empty( $request->headers->get('X-Response-Format') ) ? $format : $request->headers->get('X-Response-Format');
        $format = empty( $request->headers->get('X-Response-Passthrough') ) ? $passthrough : $request->headers->get('X-Response-Passthrough');

        // execute controller
        $response = $next( $request);

        // return response in requested format
        return match ( $format) {
            'none' => $response,
            default => $this->default( $request, $response, $passthrough),
        };
    }
}
