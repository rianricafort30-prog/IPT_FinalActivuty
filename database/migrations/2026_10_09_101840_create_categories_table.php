public function up(): void
{
Schema::create('categories', function (Blueprint $table) {
$table->id();
$table->string('name')->unique();
$table->text('description')->nullable();
$table->timestamps();
});
}

public function down(): void
{
Schema::dropIfExists('categories');
}