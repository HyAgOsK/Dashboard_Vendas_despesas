<?php

//classe dashboard
class Dashboard {

	public $data_inicio;
	public $data_fim;
	public $numeroVendas;
	public $totalVendas;
    public $clientesAtivos;
    public $clientesInativos;
    public $qntddElogios;
    public $qntddReclamacoes;
    public $totalSugestoes;
    public $todasVendasSomadas;
    public $totalDespesasSomadas;

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

	public function getNumeroVendas() {
		$query = '
			select 
				count(*) as numero_vendas 
			from 
				tb_vendas 
			where 
                data_venda BETWEEN :data_inicio AND DATE_ADD(:data_fim, INTERVAL 1 DAY)';



		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
		$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
	}

	public function getTotalVendas() {
		$query = '
			select 
				SUM(total) as total_vendas 
			from 
				tb_vendas 
			where 
                data_venda BETWEEN :data_inicio AND LAST_DAY(:data_inicio)';


		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
		$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
	}

    public function getClientesAtivos(){
        $query = '
            select
                COUNT(*) as clientes_ativos
            from
                tb_clientes
            where
                cliente_ativo = 1
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;

    }

    public function getClientesInativos(){
        $query = '
            select
                COUNT(*) as clientes_inativos
            from
                tb_clientes
            where
                cliente_ativo = 0
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
    }

    public function getElogios(){
        $query = '
            select
                COUNT(*) as quantidade_elogios
            from 
                tb_elogios
            where
                elogio = 1        
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->quantidade_elogios;
    }

    public function getReclamacoes(){
        $query = '
            select
                COUNT(*) as quantidade_reclamacoes
            from
                tb_reclamacoes
            where
                reclamacao = 1
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->quantidade_reclamacoes;
    }

    public function getTotalDespesas(){
        $query = '
            select 
                SUM(total) as total_despesas 
            from 
                tb_despesas 
            where 
                data_despesa BETWEEN :data_inicio AND LAST_DAY(:data_inicio)';


        $stmt = $this->conexao->prepare($query);
		$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
    }

    public function getTotalSugestoes(){
        $query = '
            select
                COUNT(*) as quantidade_sugestoes
            from
                tb_sugestoes
            where
                sugestoes = 1
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->quantidade_sugestoes;
    }

    public function gettotalVendasFeitas(){
        $query = '
            select
                SUM(total) as vendas_totais
            from 
                tb_vendas
            where
                data_venda BETWEEN (SELECT MIN(data_venda) FROM tb_despesas) AND :data_fim';  

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ)->vendas_totais;
    }

    public function gettotalDespesasSomadas(){
        $query = '
            select
                SUM(total) as despesas_totais
            from
                tb_despesas
            where
                data_despesa BETWEEN (SELECT MIN(data_despesa) FROM tb_despesas) AND :data_fim';  
        
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->despesas_totais;
    }
}


//lógica do script
$dashboard = new Dashboard();

$conexao = new Conexao();


$competencia = explode('-',$_GET['competencia']);
$ano = $competencia[0];
$mes = sprintf("%02d", $competencia[1]);


//calendario com a quantidade de dias de acordo com mes e ano
$dias_do_mes = cal_days_in_month(CAL_GREGORIAN,$mes,$ano);

$dashboard->__set('data_inicio', $ano . '-' . $mes . '-01');
$dashboard->__set('data_fim', $ano . '-' . $mes . '-' . $dias_do_mes . ' 23:59:59');


$bd = new Bd($conexao, $dashboard);

$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
$dashboard->__set('totalVendas', $bd->getTotalVendas());
$dashboard->__set('clientesAtivos', $bd->getClientesAtivos());
$dashboard->__set('clientesInativos', $bd->getClientesInativos());
$dashboard->__set('qntddElogios', $bd->getElogios());
$dashboard->__set('qntddReclamacoes', $bd->getReclamacoes());
$dashboard->__set('totalDespesas', $bd->getTotalDespesas());
$dashboard->__set('totalSugestoes',$bd->getTotalSugestoes());
$dashboard->__set('todasVendasSomadas',$bd->gettotalVendasFeitas());
$dashboard->__set('todasDespesasSomadas',$bd->gettotalDespesasSomadas());

echo json_encode([
    'numeroVendas' => $dashboard->__get('numeroVendas'),
    'totalVendas' => $dashboard->__get('totalVendas'),
    'clientesAtivos' => $dashboard->__get('clientesAtivos'),
    'clientesInativos' => $dashboard->__get('clientesInativos'),
    'qntddElogios' => $dashboard->__get('qntddElogios'),
    'qntddReclamacoes' => $dashboard->__get('qntddReclamacoes'),
    'totalDespesas' => $dashboard->__get('totalDespesas'),
    'totalSugestoes' => $dashboard->__get('totalSugestoes'),
    'todasVendasSomadas' => $dashboard->__get('todasVendasSomadas'),
    'todasDespesasSomadas' => $dashboard->__get('todasDespesasSomadas'),
]);
?>