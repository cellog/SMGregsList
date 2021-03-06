<?php
namespace SMGregsList\Backend\DataLayer\Sqlite3;
use SMGregsList\WriteablePlayer;
class SellPlayer extends Player implements WriteablePlayer
{

    function remove()
    {
        if (!$this->id) {
            throw new \Exception('Internal error: player is uninitialized, cannot delete it');
        }
        if (!$this->exists()) {
            return;
        }
        $data = $this->db->query("SELECT * FROM player WHERE id='" . $this->db->escapeString($this->id) . "'");
        $row = $data->fetchArray(SQLITE3_ASSOC);
        if ($this->code !== $this->manager->getCode()) {
            throw new \Exception("Error: code does not match");
        }
        $this->db->exec('BEGIN');
        $this->db->exec("DELETE FROM player WHERE id='" . $this->db->escapeString($this->id) . "'");
        $this->db->exec("DELETE FROM skills WHERE id='" . $this->db->escapeString($this->id) . "'");
        $this->db->exec("DELETE FROM stats WHERE id='" . $this->db->escapeString($this->id) . "'");
        $this->db->exec('COMMIT');
        return $this;
    }

    function save()
    {
        //$this->showme();
        if (!$this->id) {
            throw new \Exception('Player ID must be set, please try again');
        }
        $this->db->exec('BEGIN');
        $db = $this->db;
        $t = function ($a) use ($db) {
            return $db->escapeString($a);
        };
        if ($this->exists()) {
            if ($this->code !== $this->manager->getCode()) {
                throw new \Exception("Error: code does not match");
            }
            $this->db->exec("UPDATE player SET
                            age = '" . $t($this->getAge()) . "',
                            country = '" . $t($this->getCountry()) . "',
                            manager = '" . $t($this->getManager()->getName()) . "',
                            name = '" . $t($this->getName()) . "',
                            average = '" . $t($this->getAverage()) . "',
                            experience = '" . $t($this->getExperience()) . "',
                            forecast = '" . $t($this->getForecast()) . "',
                            position = '" . $t($this->getPosition()) . "',
                            progression = '" . $t($this->getProgression()) . "',
                            lastmodified = CURRENT_TIMESTAMP
                        WHERE id = '" . $t($this->getId()) . "'");
        } else {
            $this->code = $this->getManager()->getCode();
            $this->db->exec("INSERT INTO player (id, age, average, experience, forecast, position, progression, name, country, manager)
                            VALUES (
                                '" . $t($this->getId()) . "',
                                '" . $t($this->getAge()) . "',
                                '" . $t($this->getAverage()) . "',
                                '" . $t($this->getExperience()) . "',
                                '" . $t($this->getForecast()) . "',
                                '" . $t($this->getPosition()) . "',
                                '" . $t($this->getProgression()) . "',
                                '" . $t($this->getName()) . "',
                                '" . $t($this->getCountry()) . "',
                                '" . $t($this->getManager()->getName()) . "'
                                )");
        }
        $this->db->exec("DELETE FROM skills WHERE id='" . $t($this->getId()) . "'");
        $this->db->exec("DELETE FROM stats WHERE id='" . $t($this->getId()) . "'");
        foreach ($this->getSkills() as $skill => $value) {
            $this->db->exec("INSERT INTO skills (id, name, value) VALUES (
                '" . $t($this->getId()) . "',
                '" . $skill . "',
                '" . $value . "'
            )");
        }
        foreach ($this->getStats() as $stat => $value) {
            $this->db->exec("INSERT INTO stats (id, name, value) VALUES (
                '" . $t($this->getId()) . "',
                '" . $stat . "',
                '" . $value . "'
            )");
        }
        $this->db->exec('COMMIT');
        $this->createstamp = $this->db->querySingle("SELECT createstamp FROM player WHERE id='" . $t($this->getId()) . "'");
        return $this;
    }
}