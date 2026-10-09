public function up(): void
{
Schema::create('products', function (Blueprint $table) {
$table->id();
$table->foreignId('category_id')
->constrained()
->restrictOnDelete();

$table->string('name');
$table->text('description')->nullable();
$table->decimal('price', 10, 2);
$table->unsignedInteger('stock')->default(0);
$table->boolean('is_available')->default(true);
$table->timestamps();
});
}

public function down(): void
{
Schema::dropIfExists('products');
}