<?php

include_once( dirname( __FILE__ ) . '/node.php' );

class tree extends Object implements JsonSerializable
{
    /**
     * @var $elements node[] Enthält die Knoten des Baums
     */
    public $elements=array();
    
    /**
     * Liefert eine Liste der IDs der Knoten
     *
     * @return int[] die IDs
     */
    public function getIds()
    {
        return array_keys($this->elements);
    }
    
    /**
     * Liefert ein Element anhand seiner ID
     *
     * @param int $objId Die ID eines Knotens
     * @return node das Element
     */
    public function getElementById($objId)
    {
        if ($objId===null) return null;
        return $this->elements[$objId];
    }
    
    /**
     * Sucht den linken Nachbarn eines Knotens
     *
     * @param int $objId Die ID eines Knotens
     * @return int der Nachbarknoten, null im Fehlerfall
     */
    public function getLeftNeighborId($objId)
    {
        if ($elements[$objId]->parent===null) return null;
        $parent = $elements[$objId]->parent;
        $parentChilds = $elements[$parent]->childs;
        $objPos = array_search($objId, $parentChilds);
        if ($objPos===false || $objPos===0) return null;
        return $elements[$parent]->childs[$objPos-1];
    }
    
    /**
     * Sucht den linken Nachbarn eines Knotens
     *
     * @param int $objId Die ID eines Knotens
     * @return node der Nachbarknoten, null im Fehlerfall
     */
    public function getLeftNeighbor($objId)
    {
        return $this->getElementById($this->getLeftNeighborId($objId));
    }
    
    /**
     * Sucht den rechten Nachbarn eines Knotens
     *
     * @param int $objId Die ID eines Knotens
     * @return int der Nachbarknoten, null im Fehlerfall
     */
    public function getRightNeighborId($objId)
    {
        if ($elements[$objId]->parent===null) return null;
        $parent = $elements[$objId]->parent;
        $parentChilds = $elements[$parent]->childs;
        $objPos = array_search($objId, $parentChilds);
        if ($objPos===false || $objPos===count($parentChilds)-1) return null;
        return $elements[$parent]->childs[$objPos+1];
    }
    
    /**
     * Sucht den rechten Nachbarn eines Knotens
     *
     * @param int $objId Die ID eines Knotens
     * @return node der Nachbarknoten, null im Fehlerfall
     */
    public function getRightNeighboor($objId)
    {
        return $this->getElementById($this->getRightNeighborId($objId));
    }

    /**
     * Entfernt einen Teilbaum aus dem Baum
     *
     * @param int $objId Die ID des zu entfernenden Wurzelknotens
     */
    public function removeSubtree($objId)
    {
        $list = $this->extractSubtree($objId);
        $IdList = array();
        foreach ($list as $key => $elem)
            $IdList[] = $key;
            
        foreach ($this->elements as $key => $elem){
            if (in_array($key,$IdList)){
                unset($this->elements[$key]);
                continue;
            }
            
            foreach ($elem->childs as $key2 => $child)
                if (in_array($child,$IdList))
                    unset($this->elements[$key]->childs[$key2]);
        }
    }
 
    /**
     * Fügt einen Knoten in den Graphen ein
     *
     * @param string $newNode Der Name des neuen Knotens
     */
    public function add($newNode)
    {
        $elements[$newNode->id] = $newNode;
    }
    
    /**
     * setzt das $executionTime Attribut der Knoten anhand von $beginTime und $endTime,
     * sodass die $executionTime lediglich die Rechenzeit innerhalb des Knotens enthält
     */
    public function computeExecutionTime()
    {
        $tmp = $this->getIds();
        $tmp = array_reverse($tmp,true);
        foreach($tmp as $key){
            $executionTime = floor(($tmp->elements[$key]->endTime - $tmp->elements[$key]->beginTime)*1000);
            foreach ($this->elements[$key]->childs as $childKey => $child){               
                $executionTime-=$child->executionTime;
            }
            $this->elements[$key]->executionTime = $executionTime;
        }        
    }
    
    /**
     * Sucht den Vater eines Knotens
     *
     * @param int $objId Die ID eines Knotens
     * @return int Die ID des Vaters oder null im Fehlerfall.
     */
    public function getParent($objId)
    {
        if ($objId === null || !isset($this->elements[$objId])){ 
            // es handelt sich um keinen Knoten
            return null;
        }
        
        return $this->elements[$objId]->parent;
    }
    
    /**
     * Prüft, ob ein Pfad zwischen den beiden Knoten existiert
     *
     * @param int $from Die ID des Startknotens
     * @param int $to Die ID des Zielknotens
     * @param bool $inverted Invertiert die Knoten im Graph, true = Kanten umdrehen, false = Kanten unverändert
     * @return bool true = Pfad existiert, false = es existiert kein Pfad
     */
    public function pathExists($from, $to, $inverted=false)
    {
        if ($from==$to) return true;
        
        if (!$inverted){
            $currentList = array($objId=>$this->elements[$objId]);
        
            while (count($currentList>0)){
                $tmp = array_merge(array(),$currentList);
                $currentList=array();
                foreach ($tmp as $key=>$elem){
                    foreach ($this->elements[$key]->childs as $child){
                        if ($child->id==$to) return true;
                        $currentList[$child->id] = $child;
                    }
                }        
            }
        } else {
            $pos = $this->elements[$from];
            while (true){
                if ($pos->id == $to) return true;
                if ($pos->parent===null) return false;
                $pos = $this->elements[$pos->parent];
            }
        }
        
        return false;
    }
    
    /**
     * Liefert den Teilbaum, bei dem $objId die Wurzel ist
     *
     * @param int $objId Die ID eines Knotens
     * @return tree Der Teilbaum
     */
    public function extractSubtree($objId)
    {
        $resultList = array();
        $currentList = array($objId=>$this->elements[$objId]);
        
        while (count($currentList>0)){
            $tmp = array_merge(array(),$currentList);
            $currentList=array();
            foreach ($tmp as $key=>$elem){
                $resultList[$key] = $elem;
                foreach ($this->elements[$key]->childs as $child){
                    $currentList[$child->id] = $child;
                }
            }        
        }
        
        $tree = new tree();
        $tree->elements = $resultList;
        return $resultList;
    }
    
    /**
     * Sucht einen Unterbaum anhand des Knotennamens, der URI und der Aufrufmethode
     *
     * @param string $nodeName Der Knotenname
     * @param string $URI Die URI des Aufrufs
     * @param string $method Der Aufruftyp (GET, POST, ...)
     * @return int Die ID des Wurzelknotens, welcher gesucht wurde oder null (im Fehlerfall)
     */
    public function getSubtree($nodeName, $URI, $method)
    {
        foreach($this->elements as $key=>$elem){
            if ($elem->name == $nodeName && $elem->URI==$URI && $elem->method == $method)
                return $key;
        }
        return null;
    }
    
    /**
     * Prüft, ob der Baum leer ist (keine Knoten enthält)
     *
     * @return bool false = Baum enthält Knoten, true = Baum ist leer
     */
    public function emptyTree()
    {
        return count($this->elements)==0 ? true : false;
    }
    
    /**
     * sort dafür, dass die Indizes der Knoten und Kinder korrekt sortiert sind
     */
    public function sortTree()
    {
        if (!asort($this->elements)){
            // beim sortieren ist ein Fehler aufgetreten
            // machen machen machen
        }
        
        foreach($this->elements as $key => $elem){
            sort($this->elements[$key]->childs);
        }
    }
    
    /**
     * Sucht die Wurzel (aufwändig und nur im Sonderfall notwendig)
     *
     * @return int Die ID der Wurzel oder null im Fehlerfall
     */
    public function findRoot()
    {
        foreach ($this->elements as $key => $elem){
            if ($elem->parent===null){
                // der Wurzelknoten wurde gefunden
                return $key;
            }
        }
        
        // es konnte kein Wurzelknoten gefunden werden
        return null;
    }
    
    /**
     * Setzt die $label aller Elemente auf null
     */
    public function resetAllLabel()
    {
        foreach($this->elements as &$elem){
            $elem->label = null;
        }
    }
    
    /**
     * Prüft, ob das $label eines Knotens $objId einen bestimmten Wert $state hat
     *
     * @param int $objId Die ID eines Knotens
     * @param mixed $state Ein Wert, welchen das $label des Knotens haben soll
     * @return bool true = $label ist $state, false = sonst
     */
    public function isLabel($objId, $state)
    {
        if (!isset($this->elements[$objId])) return false;
        return $this->elements[$objId] == $state;
    }
    
    /**
     * Prüft, ob das $label eines Knotens $objId einen bestimmten Wert $state nicht hat
     *
     * @param int $objId Die ID eines Knotens
     * @param mixed $state Ein Wert, welchen das $label des Knotens nicht haben soll
     * @return bool true = $label ist $state, false = sonst
     */
    public function isNotLabel($objId, $state)
    {
        if (!isset($this->elements[$objId])) return true;
        return $this->elements[$objId] != $state;
    }
    
    /**
     * ???
     */
    public function jsonSerialize( )
    {
        return array( 
                     'elements'=>$this->elements
                     );
    }
}