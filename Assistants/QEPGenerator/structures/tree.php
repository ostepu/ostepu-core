<?php

/**
 * @file tree.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */
include_once(dirname(__FILE__) . '/node.php');

class tree implements JsonSerializable {

    /**
     * @var $this->elements node[] Enthält die Knoten des Baums
     */
    public $elements = array();

    /*
     * gibt die Anzahl der Element im Baum zurück
     * 
     * @return int die Anzahl
     */
    public function getTreeSize() {
        return count($this->elements);
    }

    /**
     * Liefert die Elemente des Baums
     * @return array Die Elemente
     */
    public function getElements() {
        return $this->elements;
    }
    
    /*
     * ermittelt alle Blätter des Baums
     */
    public function getLeafs(){
        $root = $this->findRoot();
        $nextElements = array($root);
        $leafs = array();
        
        for ($i=0;$i<count($nextElements);$i++){
            $child = $nextElements[$i];
            $elem = $this->getElementById($child);
            if ($elem->isLeaf()){
                $leafs[] = $elem->id;
            }
        }
        return $leafs;
    }

    /*
     * gibt die direkten Kinder des Knotens zurück
     * 
     * @param $objId die ID des Knotens
     * @return int[] die IDs der direkten Kinder
     */
    public function getChilds($objId) {
        if ($objId === null || !isset($this->elements[$objId])) {
            // das Element kann nicht gefunden werden
            return array();
        }

        return $this->elements[$objId]->childs;
    }

    /*
     * prüft, ob der Knoten Kinder besitzt
     * @param $objId die ID des Knotens
     * @return bool true = ja er hat Kinder, false = keine Kinder
     */
    public function hasChilds($objId) {
        if ($objId === null || !isset($this->elements[$objId])) {
            // das Element kann nicht gefunden werden
            return fase;
        }

        return count($this->elements[$objId]->childs) > 0;
    }

    /**
     * Setzt die Elementliste
     * @param array [$value = null] Die neue Elementliste
     */
    public function setElements($value = null) {
        $this->elements = $value;
    }

    /**
     * Liefert eine Liste der IDs der Knoten
     *
     * @return int[] die IDs
     */
    public function getIds() {
        return array_keys($this->elements);
    }

    /**
     * Liefert ein Element anhand seiner ID
     *
     * @param int  $objId Die ID eines Knotens
     * @return node das Element
     */
    public function getElementById($objId) {
        if (is_array($objId)) {
            $res = array();
            foreach ($objId as $obj) {
                if ($objId === null) {
                    return null;
                }
                $res[] = $this->elements[$obj];
            }
            return $res;
        }
        if ($objId === null) {
            return null;
        }
        if (!isset($this->elements[$objId])) {
            return null;
        }
        return $this->elements[$objId];
    }

    /**
     * Sucht den direkten linken Nachbarn eines Knotens
     *
     * @param int   $objId Die ID eines Knotens
     * @return int[] der Nachbarknoten, null im Fehlerfall
     */
    public function getPrecedingSiblingId($objId) {
        if ($this->elements[$objId]->parent === null) {
            return null;
        }
        $parent = $this->elements[$objId]->parent;
        $parentChilds = $this->elements[$parent]->childs;
        $objPos = array_search($objId, $parentChilds);
        if ($objPos === false || $objPos === 0) {
            return null;
        }
        return array($this->elements[$parent]->childs[$objPos - 1]);
    }

    /**
     * Sucht den direkten linken Nachbarn eines Knotens
     *
     * @param int    $objId Die ID eines Knotens
     * @return node[] der Nachbarknoten, null im Fehlerfall
     */
    public function getPrecedingSibling($objId) {
        return $this->getElementById($this->getPrecedingSiblingId($objId));
    }

    /**
     * Sucht den direkten rechten Nachbarn eines Knotens
     *
     * @param int   $objId Die ID eines Knotens
     * @return int[] der Nachbarknoten, null im Fehlerfall
     */
    public function getFollowingSiblingId($objId) {
        if ($this->elements[$objId]->parent === null) {
            return null;
        }
        $parent = $this->elements[$objId]->parent;
        $parentChilds = $this->elements[$parent]->childs;
        $objPos = array_search($objId, $parentChilds);
        if ($objPos === false || $objPos === count($parentChilds) - 1) {
            return null;
        }
        return array($this->elements[$parent]->childs[$objPos + 1]);
    }

    /**
     * Sucht den direkten rechten Nachbarn eines Knotens
     *
     * @param int    $objId Die ID eines Knotens
     * @return node[] der Nachbarknoten, null im Fehlerfall
     */
    public function getFollowingSibling($objId) {
        return $this->getElementById($this->getFollowingSiblingId($objId));
    }

    /**
     * Sucht alle Nachfahren eines Knotens
     *
     * @param int    $objId Die ID eines Knotens
     * @return node[] eine Liste mit Nachfahren, null im Fehlerfall
     */
    public function getDescendant($objId) {
        
    }

    /**
     * Sucht alle Vorfahren eines Knotens
     *
     * @param int    $objId Die ID eines Knotens
     * @return node[] eine Liste mit Vorfahren, null im Fehlerfall
     */
    public function getAncestor($objId) {
        
    }

    /**
     * Sucht alle rechten Nachbarn eines Knotens
     *
     * @param int    $objId Die ID eines Knotens
     * @return node[] eine Liste mit Nachbarn, null im Fehlerfall
     */
    public function getFollowingSiblings($objId) {
        
    }

    /**
     * Sucht alle linken Nachbarn eines Knotens
     *
     * @param int    $objId Die ID eines Knotens
     * @return node[] eine Liste mit Nachbarn, null im Fehlerfall
     */
    public function getPrecedingSiblings($objId) {
        
    }

    /**
     * Entfernt einen Teilbaum aus dem Baum
     *
     * @param int $objId Die ID des zu entfernenden Wurzelknotens
     */
    public function removeSubtree($objId) {
        $list = $this->extractSubtree($objId);
        $idList = array();
        foreach ($list as $key => $elem) {
            $idList[] = $key;
        }

        foreach ($this->elements as $key => $elem) {
            if (in_array($key, $idList)) {
                unset($this->elements[$key]);
                continue;
            }

            foreach ($elem->childs as $keyInner => $child) {
                if (in_array($child, $idList)) {
                    unset($this->elements[$key]->childs[$keyInner]);
                }
            }
        }
    }

    /**
     * Fügt einen Knoten in den Graphen ein
     *
     * @param string $newNode Der neue Knoten
     */
    public function addNode($newNode) {
        $this->elements[$newNode->id] = $newNode;
    }

    /**
     * Fügt eine Kante in den Graphen ein
     *
     * @param string $fromId Die ID des Startknotens
     * @param string $toId Die ID des Zielknotens
     */
    public function addEdge($fromId, $toId) {
        $elemFrom = $this->getElementById($fromId);
        $elemTo = $this->getElementById($toId);
        if ($elemFrom !== null && $elemTo !== null) {
            $elemFrom->addEdge($toId);
            $elemTo->parent = $fromId;
        }
    }

    /**
     * setzt das $executionTime Attribut der Knoten
     * anhand von $beginTime und $endTime,
     * sodass die $executionTime lediglich die Rechenzeit
     * innerhalb des Knotens enthält
     */
    public function computeExecutionTime() {
        $tmp = $this->getIds();
        $tmp = array_reverse($tmp, true);
        foreach ($tmp as $key) {
            $executionTime = $this->elements[$key]->endTime - $this->elements[$key]->beginTime;
            foreach ($this->elements[$key]->childs as $child) {
                $executionTime -= $this->elements[$child]->endTime - $this->elements[$child]->beginTime;
            }
            $this->elements[$key]->executionTime = floor($executionTime * 1000);
        }
    }

    /**
     * Sucht den Vater eines Knotens
     *
     * @param int $objId Die ID eines Knotens
     * @return int Die ID des Vaters oder null im Fehlerfall.
     */
    public function getParent($objId) {
        if ($objId === null || !isset($this->elements[$objId])) {
            // es handelt sich um keinen Knoten
            return null;
        }

        return $this->elements[$objId]->parent;
    }

    public function hasParent($objId) {
        if ($objId === null || !isset($this->elements[$objId])) {
            // es handelt sich um keinen Knoten
            return null;
        }

        return $this->elements[$objId]->parent !== null;
    }

    /**
     * Prüft, ob ein Pfad zwischen den beiden Knoten existiert
     *
     * @param int  $from     Die ID des Startknotens
     * @param int  $to       Die ID des Zielknotens
     * @param bool $inverted Invertiert die Knoten im Graph,
     *                          true = Kanten umdrehen,
     *                          false = Kanten unverändert
     * @return bool true = Pfad existiert, false = es existiert kein Pfad
     */
    public function pathExists($from, $to, $inverted = false) {
        if ($from === $to) {
            return true;
        }

        if (!$inverted) {
            $currentList = array($objId => $this->elements[$objId]);

            while (count($currentList > 0)) {
                $tmp = array_merge(array(), $currentList);
                $currentList = array();
                foreach ($tmp as $key => $elem) {
                    foreach ($this->elements[$key]->childs as $child) {
                        if ($child->id === $to) {
                            return true;
                        }
                        $currentList[$child->id] = $child;
                    }
                }
            }
        } else {
            $pos = $this->elements[$from];
            while (true) {
                if ($pos->id === $to) {
                    return true;
                }
                if ($pos->parent === null) {
                    return false;
                }
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
    public function extractSubtree($objId) {
        if ($objId === null) {
            return new tree();
        }

        $resultList = array();
        $firstElement = clone $this->elements[$objId];
        $firstElement->parent = null;
        $currentList = array($objId);

        while (count($currentList) > 0) {
            $tmp = array_merge(array(), $currentList);
            $currentList = array();
            foreach ($tmp as $key) {
                $elem = $this->getElementById($key);
                $resultList[$elem->id] = $elem;
                if (!$elem->isLeaf()) {
                    $childs = $this->elements[$key]->childs;
                    foreach ($childs as $childId) {
                        $child = $this->getElementById($childId);
                        $currentList[] = $child->id;
                    }
                }
            }
        }

        $myClass = get_class($this);
        $tree = new $myClass();
        $tree->elements = $resultList;
        return $tree;
    }

    /**
     * Sucht einen Unterbaum anhand des Knotennamens,
     *  der URI und der Aufrufmethode
     *
     * @param string $nodeName Der Knotenname
     * @param string $URI      Die URI des Aufrufs
     * @param string $method   Der Aufruftyp (GET, POST, ...)
     * @return int    Die ID des Wurzelknotens,
     *                       welcher gesucht wurde oder null (im Fehlerfall)
     */
    public function getSubtree($nodeName, $URI, $method) {
        foreach ($this->elements as $key => $elem) {
            if ($elem->name == $nodeName &&
                    $elem->URI === $URI &&
                    $elem->method === $method) {
                return $key;
            }
        }
        return null;
    }
    
    /*
     * ermittelt alle IDs der Knoten, welche sich in diesem Unterbaum befinden
     * 
     * @param int $objId die ID der Wurzel des Teilbaums welcher ermittelt werden soll
     * @return int[] die IDs der Elemente
     */
    public function getElementsInSubtree($objId){
        $elements = array($objId);
        for ($i=0;$i<count($elements);$i++){
            $key = $elements[$i];
            if ($this->hasChilds($key)){
                $elements = array_merge($elements, $this->getChilds($key));
            }
        }
        return $elements;
    }

    /**
     * Prüft, ob der Baum leer ist (keine Knoten enthält)
     *
     * @return bool false = Baum enthält Knoten, true = Baum ist leer
     */
    public function emptyTree() {
        return count($this->elements) === 0 ? true : false;
    }

    /**
     * sorgt dafür, dass die Indizes der Knoten und
     * Kinder korrekt sortiert sind
     */
    public function sortTree() {
        if (!asort($this->elements)) {
            // beim sortieren ist ein Fehler aufgetreten
            // machen machen machen
        }

        foreach ($this->elements as $key => $elem) {
            sort($this->elements[$key]->childs);
        }
    }

    /**
     * Sucht die Wurzel (aufwändig und nur im Sonderfall notwendig)
     *
     * @return int Die ID der Wurzel oder null im Fehlerfall
     */
    public function findRoot() {
        foreach ($this->elements as $key => $elem) {
            if ($elem->parent === null) {
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
    public function resetAllLabel() {
        foreach ($this->elements as &$elem) {
            $elem->label = null;
        }
    }

    /**
     * Prüft, ob das $label eines Knotens $objId
     * einen bestimmten Wert $state hat
     *
     * @param int $objId Die ID eines Knotens
     * @param mixed $state Ein Wert, welchen das $label des Knotens haben soll
     * @return bool true = $label ist $state, false = sonst
     */
    public function isLabel($objId, $state) {
        if (!isset($this->elements[$objId])) {
            return false;
        }
        return $this->elements[$objId] === $state;
    }

    /**
     * Prüft, ob das $label eines Knotens $objId
     * einen bestimmten Wert $state nicht hat
     *
     * @param int   $objId Die ID eines Knotens
     * @param mixed $state Ein Wert,
     *                      welchen das $label des Knotens nicht haben soll
     * @return bool  true = $label ist $state, false = sonst
     */
    public function isNotLabel($objId, $state) {
        if (!isset($this->elements[$objId])) {
            return true;
        }
        return $this->elements[$objId] !== $state;
    }

    /**
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct($data = array()) {
        if ($data === null) {
            $data = array();
        }

        foreach ($data as $key => $value) {
            if (isset($key)) {
                if ($key == 'elements') {
                    $tmp = node::decodeNode(
                                    $value, false
                    );

                    $this->elements = array();
                    foreach ($tmp as $elem) {
                        $this->elements[$elem->id] = $elem;
                    }
                } else {
                    $func = 'set' . strtoupper($key[0]) . substr($key, 1);
                    $methodVariable = array($this, $func);
                    if (is_callable($methodVariable)) {
                        $this->$func($value);
                    } else {
                        $this->{$key} = $value;
                    }
                }
            }
        }
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeTree($data) {
        return json_encode($data);
    }

    /**
     * decodes $data to an object
     *
     * @param string $data json encoded data (decode=true)
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
     */
    public static function decodeTree($data, $decode = true) {
        if ($decode && $data === null) {
            $data = '{}';
        }

        if ($decode) {
            $data = json_decode($data);
        }

        if (is_array($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[] = new tree($value);
            }
            return $result;
        } else {
            return new tree($data);
        }
    }

    /**
     * dient der Serialisierung des Objekts
     */
    public function jsonSerialize() {
        $list = array();
        if ($this->elements !== null && $this->elements !== array()) {
            $list['elements'] = array_values($this->elements);
        }
        return $list;
    }

}
