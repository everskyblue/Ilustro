<?php

//use Attribute;

#[Attribute]
class Column {
    private string $sql = '';
    
    public function __construct(
        ?bool $autoincrement = null,
        ?string $type_column = null,
        ?string $value_type_column = null,
        ?string $ref = null
    ) {
        
        $this->sql = match(true) {
            
        };
        
        if (is_bool($autoincrement)) {
            $this->sql .= "AUTOINCREMENT";
            print "auto";
        }
        if (is_string($type_column)) {
            print "type";
        }
        if (is_string($value_type_column)) {
            print "value";
        }
        if (is_string($ref)) {
            print "class";
        }
    }
}


class User {
    #[Column(autoincrement: true)]
    public int $id;
    #[Column(type_column: 'varchar')]
    public string $name;
    // por defecto string maximo
    public string $password;
}

class Article {
    #[Column(autoincrement: true)]
    public int $id;
    #[Column(ref: User::class)]
    public int $user_id;
    #[Column(type_column: 'char', value_type_column: 15)]
    public string $title;
    
    public string $content;
    
    public Date $created;
    
    public Date $updated;
    
}

$reflect = new ReflectionClass(Article::class);
$props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

foreach ($props as $prop) {
    $name = $prop->getName();
    $type = $prop->getType()->getName();
    $attrs = $prop->getAttributes();
    var_dump([$name, $type, array_map(fn($attribute) => $attribute->newInstance(), $attrs)]);
}

?>