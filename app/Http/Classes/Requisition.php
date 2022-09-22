<?php
class Requisition
{
    private $requisition;

    public function setRequisition($requisition)
    {
        $this->requisition = $requisition;
    }

    public function getRequisition()
    {
        return response()->json($this->requisition);
    }
}
