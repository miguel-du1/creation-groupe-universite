/**
 * Classe de gestion de la connexion à la base de données
 */
class Database
{
    private $host = "projets.iut-orsay.fr";
    private $db_name = "saes3-mdu1";
    private $username = "saes3-mdu1";
    private $password = '123';
    public $conn;

    /**
     * Établit la connexion à la base de données via PDO
     * @return PDO|null Retourne l'objet de connexion ou null en cas d'erreur
     */
    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
