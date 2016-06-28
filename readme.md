# Konzole pro Nette Framework

Nastavení v **config.neon**
```neon
extensions:
    console: NAtrreid\Console\DI\ConsoleExtension
```

dostupné nastavení
```neon
console:
    prefix: cli
    commands:
        - TridaSPrikazy
        - TridaSPrikazy2
```

## Příkazy
```php
class TridaSPrikazy extends CommandCollection {

    /**
     * Popis, zobrazi se v napovede
     * @param string $promena i tento text se zobrazi v napovede
     */
    public function prikaz($promena) {
        $this->printLine('Provadim prikaz');
        // php kod
    }
}
```

## Spouštění
Spouštení přes příkazový řádek
```bash
php index.php tridasprikazy:prikaz /promena=hodnota
```

nebo přes prohlížeč, pokud je zapnuta Tracy. 
```
http://domena/cli/tridasprikazy/prikaz?promena=hodnota
```