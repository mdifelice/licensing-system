<?php
class Message {
	private $text,
			$type;

	public function __construct( $text, $type ) {
		$this->text = $text;
		$this->type = $type;
	}

	public function render() {
		?>
<div class="alert alert-<?php echo htmlspecialchars( $this->type ); ?>"><?php echo htmlspecialchars( $this->text ); ?></div>
		<?php
	}
}
