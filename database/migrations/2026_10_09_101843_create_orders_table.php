public function up(): void
{
Schema::create('orders', function (Blueprint $table) {
$table->id();
$table->foreignId('customer_id')
->constrained()
->restrictOnDelete();

$table->string('order_number')->unique();
$table->dateTime('ordered_at');
$table->decimal('total', 12, 2)->default(0);
$table->string('status')->default('pending');
$table->text('notes')->nullable();
$table->timestamps();
});
}

public function down(): void
{
Schema::dropIfExists('orders');
}