<?php
namespace entity;
use entity\Battle;
use repository\BattleDao;


class BattleCore
{
    
    function routePhaseWithCpu($battleAux, $phase, $enemy, $playerOne, $phaseDao, $battleDao)
    {   
        $recentPhase = is_array($battleAux) ? $phaseDao->findPhase($battleAux['uuid']) : $phaseDao->findPhase($battleAux);
        
        $isHeroOne    = $recentPhase['id_hero_one'] == $battleAux['id_hero_one'];
        $isHeroTwo    = $recentPhase['id_hero_one'] == $battleAux['id_hero_two'];
        $isFirstPhase = $recentPhase == null && is_array($battleAux);

    
        if ($isFirstPhase) {
            $phase->setDescription("Iniciou o combate entre o heroi " . $playerOne['name'] . " e o Heroi " . $enemy['name']);
            $this->passPhase($phase, $battleAux, $battleAux['id_hero_one'], $phaseDao);
            return;
        }

        if ($isHeroOne) {
            $random = $this->rollD6($enemy['atk']);
            $phase  = $this->damage($battleAux, $phase, $random, $playerOne, $battleDao);
            $this->passPhase($phase, $battleAux, $battleAux['id_hero_two'], $phaseDao);
            return;
        }
        
        if ($isHeroTwo) {
            $random = $this->rollD6($playerOne['atk']);
            $phase  = $this->damage($battleAux, $phase, $random, $enemy, $battleDao);
            $this->passPhase($phase, $battleAux, $playerOne['uuid'], $phaseDao);
            return;
        }
    }
    
    function rollD6($atk)
    {
        return rand(1, $atk) + rand(1, 6);
    }
    
    function passPhase($phase, $battle, $player, $phaseDao)
    {
        $phase->setBattle_id($battle['uuid']);
        $phase->setPlayer_id($player);
        $phaseDao->insert($phase);
    }
    
    function damage($battle, $phase, $random, $hero, $battleDao)
    {
        $esquivou = rand(0, $hero['agility']) == $hero['agility'];
        
        if ($esquivou) {
            $phase->setDescription("O Herói " . $hero['name'] . " esquivou.");
            return $phase;
        }
        $damage = $this->critical($random);
        if ($hero['uuid'] == $battle['id_hero_one']) {
            $healthPoints = $battle['hp_hero_one'] - $damage;
            $battleDao->hpMinusPlayerOne($healthPoints, $battle['uuid']);
        }
        
        if ($hero['uuid'] == $battle['id_hero_two']) {
            $healthPoints = $battle['hp_hero_two'] - $damage;
            $battleDao->hpMinusPlayerTwo($healthPoints, $battle['uuid']);
        }
        $phase->setDescription("O Herói " . $hero['name'] . " recebeu " . $damage . " de dano crítico.");
        
        return $phase;
    }
    
    function critical($damage)
    {
        $critical = rand(1, 6);
        $damage   = $critical == 6 ? $damage * 2 : $damage;
        return $damage;
    }
}