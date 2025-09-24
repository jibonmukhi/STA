<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');

            // Certificate Basic Information
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('subject');
            $table->string('certificate_number')->unique();

            // Dates
            $table->date('issue_date');
            $table->date('expiration_date');
            $table->integer('duration_months')->nullable(); // Duration in months

            // Training Organization
            $table->string('training_organization');
            $table->string('training_organization_code')->nullable();
            $table->string('instructor_name')->nullable();
            $table->text('training_organization_address')->nullable();

            // Certificate Details
            $table->string('certificate_type')->default('training'); // training, qualification, compliance, etc.
            $table->string('level')->nullable(); // beginner, intermediate, advanced, professional
            $table->decimal('hours_completed', 8, 2)->nullable(); // Training hours
            $table->decimal('credits', 8, 2)->nullable(); // Credit hours/points
            $table->decimal('score', 5, 2)->nullable(); // Exam/assessment score
            $table->string('grade')->nullable(); // Pass/Fail, A/B/C, etc.

            // Compliance & Regulatory
            $table->string('regulatory_body')->nullable(); // OSHA, FDA, etc.
            $table->string('compliance_standard')->nullable(); // ISO 9001, etc.
            $table->boolean('renewal_required')->default(true);
            $table->integer('renewal_period_months')->nullable();
            $table->date('next_renewal_date')->nullable();

            // Status and Verification
            $table->enum('status', ['active', 'expired', 'revoked', 'pending', 'suspended'])->default('active');
            $table->string('verification_code')->unique();
            $table->string('issuer_signature')->nullable();
            $table->timestamp('verified_at')->nullable();

            // File Attachments
            $table->string('certificate_file_path')->nullable(); // PDF certificate
            $table->string('transcript_file_path')->nullable(); // Academic transcript
            $table->json('supporting_documents')->nullable(); // Additional documents

            // Additional Fields
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional flexible data
            $table->boolean('is_public')->default(false); // Public visibility
            $table->string('language')->default('en'); // Certificate language

            // Audit Fields
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index(['expiration_date', 'status']);
            $table->index(['certificate_type', 'status']);
            $table->index(['training_organization']);
            $table->index('verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};