<?php
/**
 * @file cacheTree.php
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */


include_once(dirname(__FILE__) . '/structures/tree.php');

class cacheTree extends tree implements JsonSerializable
{
    /**
     * @var $_dependenced int[] Enthält den Status
     * (frei = 0, abhängig = 1) der Knoten
     */
    private $_dependenced=array();

    /**
     * @var $_groups int[][]
     * Enthält die Gruppen und die IDs der zugehörigen Knoten
     * Struktur: $_groups[Gruppennummer] = array(Mitglied0,Mitglied1,Mitglied2)
     */
    private $_groups=array();

    /**
     * @var $_mapMethods int[]
     * Wandelt Anfragetypen in deren rechnerischen Status um
     * Struktur: $_mapMethods[Anfragetyp] = Abhängigkeit
     */
    private static $_mapMethods = array('GET'=>0,
                                        'HEAD'=>0,
                                        'POST'=>1,
                                        'PUT'=>1,
                                        'DELETE'=>1,
                                        'DEFAULT'=>1);

    /**
     * Berechnet die Gruppenzugehörigkeit der Knoten und speichert
     * diese in $this->groups
     */
    public function computeGroups()
    {
        // entferne alle bisher berechneten Gruppen
        $this->resetGroups();

        foreach ($this->elements as $key => $elem) {
            $groupId = $elem->parallelGroup;
            if ($groupId!==null) {
                if (!isset($this->groups[$groupId])) {
                    // die Gruppe wurde bisher noch nicht bearbeitet,
                    // daher muss ein neuer Slot angelegt werden
                    $this->groups[$groupId] = array();
                }

                // trage das Element in seine Gruppe ein
                $this->groups[$groupId][] = $key;
            }
        }
    }

    /**
     * setzt die berechneten Gruppen auf den Urzustand zurück
     */
    private function resetGroups()
    {
        $this->groups=array();
    }

    /**
     * Liefert den rechnerischen Status einer Aufrufmethode
     *
     * @param string $method Der Bezeichner einer HTTP Aufrufmethode
     * (GET,DELETE,PUT,HEAD,POST,...)
     * @return int Der Status der Methode (0 = frei, 1 = abhängig)
     */
    public function getMethodState($method)
    {
        $method = strtoupper($method);

        if (!isset(self::$_mapMethods[$method])) {
            // die Methode ist nicht bekannt, daher wird der DEFAULT verwendet
            return self::$_mapMethods['DEFAULT'];
        }

        // die Methode ist bekannt und kann daher übersetzt werden
        return self::$_mapMethods[$method];
    }

    /**
     * Der Zustand zu einem Knoten, anhand dessen ID.
     *
     * @param int $objId Die ID eines Knotens
     * @return int Gibt den Zustand des Elements zurück
     *                                  (0 = frei, 1 = abhängig)
     */
    public function getElementState($objId)
    {
        if ($objId===null || !isset($this->elements[$objId])) {
            // es handelt sich um keinen Knoten
            return 0;
        }

        if (!isset($_dependenced[$objId])) {
            // der Status des Elements ist nicht bekannt
            return 0;
        }

        return $_dependenced[$objId];
    }

    /**
     * Der Zustand zu einem Knoten, anhand der Zustände der Kinder.
     *
     * @param int $objId Die ID eines Knotens
     * @return int Gibt den Zustand des Elements zurück
     *                                  (0 = frei, 1 = abhängig)
     */
    public function getChildsState($objId)
    {
        if ($objId===null || !isset($this->elements[$objId])) {
            // es handelt sich um keinen Knoten
            return 0;
        }

        foreach ($this->elements[$objId]->childs as $childId) {
            if (isset($_dependenced[$childId]) &&
                $_dependenced[$childId] === 1) {
                // das Kind ist abhängig, dieser Zustand dominiert
                return 1;
            }
        }

        return 0;
    }

    /**
     * Ob ein Knoten berechenbare Kinder besitzt
     *
     * @param int $objId Die ID eines Knotens
     * @return bool Ob es berechenbare Kinder gibt (true = ja, false = nein)
     */
    public function hasComputableChild($objId)
    {
        if ($objId===null || !isset($this->elements[$objId])) {
            // es handelt sich um keinen Knoten
            return 0;
        }

        foreach ($this->elements[$objId]->childs as $childId) {
            if (isset($_dependenced[$childId]) &&
                $_dependenced[$childId] === 0) {
                // es gibt ein gültiges berechenbares Kind
                return true;
            }
        }

        return false;
    }

    /**
     * Liefert den rechnerischen Status des Knotens, welcher anhand
     * des Aufruftyps entsteht (parallel oder seriell)
     *
     * @param int $objId Die ID eines Knotens
     * @return int Der resultierende Status (0 = frei, 1 = abhängig)
     */
    public function getElementTypeState($objId)
    {
        if ($objId === null || !isset($this->elements[$objId])) {
            // es handelt sich um keinen Knoten
            return 0;
        }

        $parent = $this->getParent($objId);
        if ($parent !== null) {
            // der Knoten hat einen Vater
            if (count($this->elements[$parent]->childs) === 1) {
                // der Knoten $objId ist das einzige Kind und kann daher
                // parallel ausgeführt werden
                return 0;
            }
        } else {
            // der Knoten hat keinen Vater und kann
            // daher parallel ausgeführt werden
            return 0;
        }

        return $this->getElementType($objId);
    }

    /**
     * Liefert den Typ des Aufrufs (parallel oder seriell)
     *
     * @param int $objId Die ID eines Knotens
     * @return int Der Aufruftyp des Knotens
     *                           (0 = parallel, 1 = seriell), Fehler = 0
     */
    public function getElementType($objId)
    {
        if ($objId === null || !isset($this->elements[$objId])) {
            // es handelt sich um keinen Knoten
            return 0;
        }

        $groupId = $this->elements[$objId]->parallelGroup;
        if ($groupId === null || $this->getGroupSize($groupId) === 1) {
            // es ist ein serieller Aufruf
            return 1;
        }

        // der Knoten gehört einer Gruppe an
        return 0;
    }

    /**
     * Liefert die Anzahl der Knoten in einer Gruppe
     *
     * @param int $groupId Die ID einer Knotengruppe
     * @return int Die größe der Gruppe
     */
    public function getGroupSize($groupId)
    {
        if ($groupId === null || !isset($this->groups[$groupId])) {
            // es handelt sich um keine Gruppe
            return 0;
        }

        return count($this->groups[$groupId]);
    }

    /**
     * Berechnet die Abhängigkeiten aller Knoten
     * und speichert diese in $this->dependenced
     */
    public function computeDependencies()
    {
        $this->resetDependencies();
        $this->computeGroups();

        $keys = $this->getIds();
        $firstKey = array_shift($keys);
        $_dependenced[$firstKey]=0;

        foreach ($keys as $key) {
            $parentState = $this->getElementState($this->getParent($key));
            $previousElementState = $this->getElementState(
                $this->getLeftNeighborId(
                    $key
                )
            );
            $typeState = $this->getElementTypeState($key);
            $methodState = $this->getMethodState($key);
            $this->dependenced[$key] = max(
                $parentState,
                $previousElementState,
                $typeState,
                $methodState
            );
        }
    }

    /**
     * Liefert die reduzierten berechenbaren Knoten
     *
     * @return int[] Die reduzierten berechenbaren Knoten
     */
    public function extractMinComputable()
    {
        $list = $this->extractComputable();
        return $this->minimizeComputable($list);
    }

    /**
     * Liefert die berechenbaren Knoten
     *
     * @return int[] Die berechenbaren Knoten
     */
    public function extractComputable()
    {
        $list = array();
        foreach ($this->dependenced as $key => $value) {
            if (!$value) {
                $list[$key];
            }
        }
        return $list;
    }

    /**
     * Prüft, ob ein Knoten berechenbare Kinder besitzt
     * (sodass seine Berechnung nicht notwendig ist) und entfernt
     * diese Knoten.
     *
     * @param int[] $list Eine Liste von Knoten IDs
     * @return int[] Die reduzierte Liste von Knoten IDs
     */
    private function minimizeComputable($list)
    {
        if (!sort($list)) {
            // beim sortieren ist ein Fehler aufgetreten,
            // also soll das Element mit der kleinsten
            // berechenbaren ID verwendet werden (sollte root sein)
            return array(min($list));
        }

        foreach ($list as $key => $objId) {
            if ($this->hasComputableChild($objId)) {
                // der Knoten bestizt berechenbare Kinder und kann
                // daher entfernt werden
                unset($list[$key]);
            }
        }
        return array_values($list);
    }

    /**
     * setzt die berechneten Abhängigkeiten auf den Urzustand zurück
     */
    private function resetDependencies()
    {
        $this->dependenced = array();
    }

    /**
     * dient der Serialisierung des Objekts
     */
    public function jsonSerialize()
    {
        return parent::jsonSerialize();
    }
}
