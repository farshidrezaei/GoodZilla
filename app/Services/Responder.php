<?php


namespace App\Services;


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;


class Responder
{

    /**
     * @var array
     */
    protected array        $body    = [];
    protected string       $message = '';
    protected int          $status  = 200;
    protected array        $metas   = [];
    protected JsonResponse $response;


    /**
     * @param $body
     *
     * @return $this
     */
    public function body( $body ): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function message( string $message ): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function status( int $status ): self
    {
        $this->status = $status;
        return $this;
    }


    /**
     * @return Responder
     */
    public function json(): Responder
    {
        $this->response = response()->json( [
            'body' => $this->body,
            'message' => $this->message,
            'status' => $this->status,
            'metas' => $this->metas,
        ], $this->status );
        return $this;
    }


    public function send(): void
    {
        throw new HttpResponseException( $this->response );
    }


}
