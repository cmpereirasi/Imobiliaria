<?php

use Core\Controller;
use Validate\Funcionario\Add as Add;
use Validate\Sanitize;
use Auth\Auth;

class FuncionarioController extends Controller
{
    private $controller = 'Funcionario';

    public function __construct()
    {
        // Herdando Construct
        parent::__construct();
        $this->Funcionario = parent::loadModel("Funcionario");
    }

    public function index()
    {
        // Instanciando a classe
        $auth = new Auth();
        
        // Verificando se o usuário está logado
        if ($auth->verifyAuthenticated()) {
            // Pegando dados
            $funcionarios = $this->Funcionario->list();

            // Carregando Model
            $this->CategoriaFuncionario = parent::loadModel("CategoriaFuncionario");

            // Pegando contagem de total de itens
            $count = $this->Funcionario->countItems();

            // Caregando Helpers
            $bootstrapHelper = parent::loadHelper("Bootstrap");
            $styleHelper = parent::loadHelper("Style");
            $linkHelper = parent::loadHelper("Link");

            // Carregando View
            require_once parent::loadView($this->controller, $this->currentAction);
            exit;
        }

        // Redirecionando para o login
        $this->redirectUrl('Acesso/Login');
        exit;
    }

    public function add()
    {
        // Instanciando a classe
        $auth = new Auth();
        
        // Verificando se o usuário está logado
        if ($auth->verifyAuthenticated()) {
            // Chamando Validação
            $funcionarioValidate = new Add();
            $funcionarioValidate->validate();

            // Pegando Dados da requisição de forma dinâmica e automática
            $sanitized = new Sanitize();
            $data = $sanitized->sanitized();

            // Carregando Model
            $this->CategoriaFuncionario = parent::loadModel("CategoriaFuncionario");

            // Pegando dados
            $categorias = $this->CategoriaFuncionario->list();
            $categoriasOption = [];
            foreach ($categorias as $categoria) {
                $option = null;
                $value = $categoria->cd_categoria;
                $text = $categoria->nm_categoria;
                $option["value"] = $value;
                $option["text"] = $text;
                $categoriasOption[] = $option;
            }

            // Verificando erro de Validação
            if (!$funcionarioValidate->hasErrors()) {
                // Carregando Model
                $this->Pessoa = parent::loadModel("Pessoa");

                $this->Pessoa->nm_primeiro = $data->nm_primeiro;
                $this->Pessoa->nm_meio = $data->nm_meio;
                $this->Pessoa->nm_ultimo = $data->nm_ultimo;
                $this->Pessoa->dt_nascimento = $data->dt_nascimento;
                $this->Pessoa->dt_criado = date("Y-m-d H:i:s");
                $this->Pessoa->dt_editado = date("Y-m-d H:i:s");
                $this->Pessoa->cd_cpf = $data->cd_cpf;
                $this->Pessoa->insert();

                $cd_pessoa = $this->Pessoa->lastId;
                
                $this->Funcionario->setIcStatus($data->ic_status);
                $this->Funcionario->cd_categoria = $data->cd_categoria;
                $this->Funcionario->cd_creci = $data->cd_creci;
                $this->Funcionario->cd_pessoa = $cd_pessoa;
                $this->Funcionario->insert();

                // Redirecionando para a action index
                $this->redirectUrl($this->controller);
                exit;
            }

            // Caregando Helpers
            $bootstrapHelper = parent::loadHelper("Bootstrap");
            $styleHelper = parent::loadHelper("Style");
            $linkHelper = parent::loadHelper("Link");
            $formHelper = parent::loadHelper("Form");

            // Carregando View
            require_once parent::loadView($this->controller, $this->currentAction);
            exit;
        }

        // Redirecionando para o login
        $this->redirectUrl('Acesso/Login');
        exit;
    }

    public function edit(array $param)
    {
        // Instanciando a classe
        $auth = new Auth();
        
        // Verificando se o usuário está logado
        if ($auth->verifyAuthenticated()) {
            // Pegando valor do parâmetro
            $cd_funcionario = $param[0];

            // Verificando se o código está preechido
            if (!empty($cd_funcionario)) {

                // Chamando Validação
                $funcionarioValidate = new Add();
                $funcionarioValidate->validate();

                // Pegando Dados da requisição de forma dinâmica e automática
                $sanitized = new Sanitize();
                $data = $sanitized->sanitized();

                // Carregando Model
                $this->CategoriaFuncionario = parent::loadModel("CategoriaFuncionario");

                // Pegando dados
                $categorias = $this->CategoriaFuncionario->list();
                $categoriasOption = [];
                foreach ($categorias as $categoria) {
                    $option = null;
                    $value = $categoria->cd_categoria;
                    $text = $categoria->nm_categoria;
                    $option["value"] = $value;
                    $option["text"] = $text;
                    $categoriasOption[] = $option;
                }

                // Definindo Código
                $this->Funcionario->cd_funcionario = $cd_funcionario;

                // Buscando Dados
                $funcionario = $this->Funcionario->select();

                // Carregando Model
                $this->Pessoa = parent::loadModel("Pessoa");

                // Formatando Status para formulário
                if ($funcionario->ic_status == 1) {
                    $ic_status = 'enable';
                } else {
                    $ic_status = 'disable';
                }

                // Verificando erro de Validação
                if (!$funcionarioValidate->hasErrors()) {
                    $this->Funcionario->setIcStatus($data->ic_status);
                    $this->Funcionario->cd_categoria = $data->cd_categoria;
                    $this->Funcionario->cd_creci = $data->cd_creci;
                    $funcionario = $this->Funcionario->select();
                    $cd_pessoa = $funcionario->cd_pessoa;
                    $this->Funcionario->cd_pessoa = $cd_pessoa;
                    $this->Funcionario->update();
                    $this->Pessoa->cd_pessoa = $cd_pessoa;
                    $this->Pessoa->nm_primeiro = $data->nm_primeiro;
                    $this->Pessoa->nm_meio = $data->nm_meio;
                    $this->Pessoa->nm_ultimo = $data->nm_ultimo;
                    $this->Pessoa->dt_nascimento = $dt_nascimento;
                    $this->Pessoa->dt_criado = $funcionario->dt_criado;
                    $this->Pessoa->dt_editado = date("Y-m-d H:i:s");
                    $this->Pessoa->cd_cpf = $data->cd_cpf;
                    $this->Pessoa->update();

                    // Redirecionando para a action index
                    $this->redirectUrl($this->controller);
                    exit;
                }
                // Caregando Helpers
                $bootstrapHelper = parent::loadHelper("Bootstrap");
                $styleHelper = parent::loadHelper("Style");
                $linkHelper = parent::loadHelper("Link");
                $formHelper = parent::loadHelper("Form");

                // Carregando View
                require_once parent::loadView($this->controller, $this->currentAction);
                exit;
            }
            $this->redirectUrl();
            exit;
        }

        // Redirecionando para o login
        $this->redirectUrl('Acesso/Login');
        exit;
    }

    public function view(array $param)
    {
        // Instanciando a classe
        $auth = new Auth();
        
        // Verificando se o usuário está logado
        if ($auth->verifyAuthenticated()) {
            // Pegando valor do parâmetro
            $cd_funcionario = $param[0];

            // Verificando se o código está preechido
            if ($cd_funcionario != "") {
                // Definindo Código
                $this->Funcionario->cd_funcionario = $cd_funcionario;

                // Carregando Model
                $this->CategoriaFuncionario = parent::loadModel("CategoriaFuncionario");

                // Buscando Dados
                $funcionario = $this->Funcionario->select();

                // Definindo Código
                $this->CategoriaFuncionario->cd_categoria = $funcionario->cd_categoria;

                // Buscando Dados
                $categoria = $this->CategoriaFuncionario->select();
                $funcionario->nm_categoria = $categoria->nm_categoria;

                // Caregando Helpers
                $bootstrapHelper = parent::loadHelper("Bootstrap");
                $styleHelper = parent::loadHelper("Style");
                $linkHelper = parent::loadHelper("Link");

                // Carregando View
                require_once parent::loadView($this->controller, $this->currentAction);
                exit;
            }
            $this->redirectUrl();
            exit;
        }
        
        // Redirecionando para o login
        $this->redirectUrl('Acesso/Login');
        exit;
    }

    public function disable(array $param)
    {
        // Instanciando a classe
        $auth = new Auth();
        
        // Verificando se o usuário está logado
        if ($auth->verifyAuthenticated()) {
            // Pegando valor do parâmetro
            $cd_funcionario = $param[0];

            // Verificando se o código está preechido
            if (!empty($cd_funcionario)) {
                // Definindo Código
                $this->Funcionario->cd_funcionario = $cd_funcionario;

                // Desabilitando unidade
                $this->Funcionario->disable();
                $this->redirectUrl();
                exit;
            }

            $this->redirectUrl();
            exit;
        }

        // Redirecionando para o login
        $this->redirectUrl('Acesso/Login');
        exit;
    }

    public function enable(array $param)
    {
        // Instanciando a classe
        $auth = new Auth();
        
        // Verificando se o usuário está logado
        if ($auth->verifyAuthenticated()) {
            // Pegando valor do parâmetro
            $cd_funcionario = $param[0];

            // Verificando se o código está preechido
            if (!empty($cd_funcionario)) {
                // Definindo Código
                $this->Funcionario->cd_funcionario = $cd_funcionario;

                // Habilitando unidade
                $this->Funcionario->enable();
                $this->redirectUrl();
                exit;
            }

            $this->redirectUrl();
            exit;
        }

        // Redirecionando para o login
        $this->redirectUrl('Acesso/Login');
        exit;
    }

    public function csv()
    {

        // Instanciando a classe
        $auth = new Auth();
        
        // Verificando se o usuário está logado
        if ($auth->verifyAuthenticated()) {
            echo getcwd();
            if ($_FILES) {
                var_dump($_FILES);
                $destino = "Upload/" . $_FILES["csv"]["name"];
                $status = move_uploaded_file(
                    $_FILES["csv"]["tmp_name"],
                    $destino);
                var_dump($destino);
                if ($status) {
                    $file = fopen("Upload/".$_FILES["csv"]["name"],"r");
                    if ($file) {
                        $line = fgetcsv($file, 100, ";");
                        $line = fgetcsv($file, 100, ";");
                        $this->Pessoa = parent::loadModel("Pessoa");
                        while($line != null) {
                            $this->Pessoa->nm_primeiro = $line[0];
                            $this->Pessoa->nm_meio = $line[1];
                            $this->Pessoa->nm_ultimo = $line[2];
                            $this->Pessoa->dt_nascimento = $line[3];
                            $this->Pessoa->dt_criado = date("Y-m-d H:i:s");
                            $this->Pessoa->dt_editado = date("Y-m-d H:i:s");
                            $this->Pessoa->cd_cpf = $line[4];
                            $this->Pessoa->insert();

                            $cd_pessoa = $this->Pessoa->lastId;
                            
                            $this->Funcionario->ic_status = $line[5];
                            $this->Funcionario->cd_categoria = $line[6];
                            $this->Funcionario->cd_creci = $line[7];
                            $this->Funcionario->cd_pessoa = $cd_pessoa;
                            $this->Funcionario->insert();
                            $nome = $line[1];
                            $line = fgetcsv($file, 100, ";");
                        }
                        fclose($file);
                        exit;
                    }
                }

                
            }

            // Caregando Helpers
            $bootstrapHelper = parent::loadHelper("Bootstrap");
            $styleHelper = parent::loadHelper("Style");
            $linkHelper = parent::loadHelper("Link");

            // Carregando View
            require_once parent::loadView($this->controller, $this->currentAction);
            exit;
        }
        
        // Redirecionando para o login
        $this->redirectUrl('Acesso/Login');
        exit;
    }

}
