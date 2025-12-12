<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class Formazione extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
    public function index()
    {


        // Questa riga semplicemente restituisce la vista 'home.blade.php'
        // Replicando il comportamento di Route::view, ma all'interno del pipeline di un Controller.
        return view('home');

        // $colori = new ColoriBase('blu', 'verde', 'giallo', 'rosso', 'viola');
        // $colori->setGiallo('Ikea');
        // $colore = $colori->getGiallo();

        // return $colore;
    }
}

class ColoriBase extends DTOBaseArcobaleno
{
    private $viola;

    public function __construct($viola, $blu, $verde, $giallo, $rosso)
    {
        $this->viola = $viola;
        parent::__construct($blu, $verde, $giallo, $rosso);
    }

    public function getViola()
    {
        return $this->viola;
    }

    public function setViola($viola)
    {
        $this->viola = $viola;
    }

    public function getColore()
    {
        return parent::getColore() . $this->viola;
    }
}



abstract class DTOBaseArcobaleno
{

    private $blu;
    private $verde;
    private $giallo;
    private $rosso;

    public function __construct($blu, $verde, $giallo, $rosso)
    {
        $this->blu = $blu;
        $this->verde = $verde;
        $this->giallo = $giallo;
        $this->rosso = $rosso;
    }

    public function getBlu()
    {
        return $this->blu;
    }

    public function getVerde()
    {
        return $this->verde;
    }

    public function getGiallo()
    {
        return $this->giallo;
    }

    public function getRosso()
    {
        return $this->rosso;
    }

    public function setBlu($blu)
    {
        $this->blu = $blu;
    }

    public function setVerde($verde)
    {
        $this->verde = $verde;
    }

    public function setGiallo($giallo)
    {
        $this->giallo = $giallo;
    }

    public function setRosso($rosso)
    {
        $this->rosso = $rosso;
    }

    public function getColore()
    {
        return $this->blu . $this->verde . $this->giallo . $this->rosso;
    }
}
