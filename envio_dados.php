<?php
if(!isset($_POST)){
    //classe dashboard
    class Dashboard {
        public $apartamento;
        public $reclamacao;
        public $elogio;
        public $sugestao;
        public $valor;
        public $bloco;
        public $numero;

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
            return $this;
        }
    }

    //classe de conexão bd
    class Conexao {
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $pass = '';

        public function conectar() {
            try {

                $conexao = new PDO(
                    "mysql:host=$this->host;dbname=$this->dbname",
                    "$this->user",
                    "$this->pass"
                );

                //
                $conexao->exec('set charset utf8');

                return $conexao;

            } catch (PDOException $e) {
                echo '<p>'.$e->getMessege().'</p>';
            }
        }
    }

    //classe (model)
    class Bd {
        private $conexao;
        private $dashboard;

        public function __construct(Conexao $conexao, Dashboard $dashboard) {
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function setApartamento(){
            $query = 'INSERT INTO tb_apartamento (bloco, numero) VALUES (:bloco, :numero)';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':bloco', $this->dashboard->bloco);
            $stmt->bindValue(':numero', $this->dashboard->numero);
            $stmt->execute();
        }
        
        public function setValorInvestido() {
            $query = 'insert into tb_vendas (data_venda, total) values(now(), :valor)';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':valor', $this->dashboard->valor);
            $stmt->execute();
        }
        
        public function setClienteAtivoouInativo(){
            $query = '
                insert into 
                    tb_clientes(cliente_ativo) 
                values(:cliente_ativo) 
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':cliente_ativo', $this->dashboard->clienteAtivo); 
            $stmt->execute();
        }


        public function setElogios(){
            $query = '
                insert into 
                    tb_elogios(elogio,descricao_elogio) 
                values(:elogio, :descricao_elogio) 
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':elogio', $this->dashboard->elogio);
            $stmt->bindValue(':descricao_elogio', $this->dashboard->descricaoElogio);
            $stmt->execute();
        }

        public function setReclamacoes(){
            $query = '
                insert into 
                    tb_reclamacoes(reclamacao,descricao_reclamacao) 
                values(:reclamacao, :descricao_reclamacao) 
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':reclamacao', $this->dashboard->reclamacao);
            $stmt->bindValue(':descricao_reclamacao', $this->dashboard->descricaoReclamacao);
            $stmt->execute();
        }

        public function setSugestoes(){
            $query = '
                insert into 
                    tb_sugestoes(sugestoes,descricao_sugestao) 
                values(:sugestoes, :descricao_sugestao) 
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':sugestoes', $this->dashboard->sugestao);
            $stmt->bindValue(':descricao_sugestao', $this->dashboard->descricaoSugestao);
            $stmt->execute();
        }

        public function setDespesas(){
            $query = '
            insert into 
                tb_despesas(data_despesa,total) 
            values(now(), :total) 
        ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':total', $this->dashboard->totalDespesas);
            $stmt->execute();
        }
    }

    $dashboard = new Dashboard();
    $conexao = new Conexao();

    $dashboard->bloco = $_POST['bloco'];
    $dashboard->numero = $_POST['numero'];
    $dashboard->valor = $_POST['numeroVenda'];
    $dashboard->clienteAtivo = $_POST['clienteAtivo'];
    $dashboard->reclamacao = $_POST['reclamacoes'];
    $dashboard->descricaoReclamacao = $_POST['reclamacao'];
    $dashboard->elogio = $_POST['elogios'];
    $dashboard->descricaoElogio = $_POST['elogio'];
    $dashboard->sugestao = $_POST['sugestoes'];
    $dashboard->descricaoSugestao = $_POST['sugestao'];
    $dashboard->totalDespesas = $_POST['despesasNumero'];

    $bd = new Bd($conexao, $dashboard);

    $bd->setApartamento();
    $bd->setValorInvestido();
    $bd->setClienteAtivoouInativo();
    $bd->setReclamacoes();
    $bd->setElogios();
    $bd->setSugestoes();
    $bd->setDespesas();

    header('Location: index.html');
}else{
    header('Location: index.html');
}

?>