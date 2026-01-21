@extends('layouts.app')

@section('title', 'แก้ไขตัวบ่งชี้')

@php
    $ind = $data_indicator ?? [];
    $info = $information ?? [];

    $id = $ind['id'] ?? null;
    $year = $ind['year'] ?? '';
    $name = $ind['name'] ?? '';
    $code = $ind['code'] ?? '';
    $maxScore = $ind['max_score'] ?? '';
    $deadline = $ind['deadline'] ?? '';

    $rawType = $ind['type'] ?? '';

    $std = $ind['standard']['id'] ?? null;
    $category = $ind['category']['id'] ?? null;

    // richtexts
    $desc = $ind['description'] ?? '';
    $cond = $ind['condition'] ?? '';
    $comment = $ind['comment'] ?? '';
    $note = $ind['annotation'] ?? '';

    $standards = $info['standards'] ?? [];
    $categories = $info['categories'] ?? [];
    $categoriesByStandard = $info['categoriesByStandard'] ?? [];

    $departments = $info['departments'] ?? [];
    $affiliations = $info['affiliations'] ?? [];
    $usersForAssign = $info['usersForAssign'] ?? [];

    $depSelected = collect($ind['departments'] ?? [])
        ->pluck('id')
        ->map(fn($v) => (string) $v)
        ->values()
        ->all();
    $workGroupSelected = collect($ind['departments'] ?? [])
        ->pluck('work_group')
        ->filter()
        ->unique()
        ->map(fn($v) => (string) $v)
        ->values()
        ->all();
    $userSelected = collect($ind['assignments'] ?? [])
        ->pluck('user.id')
        ->map(fn($v) => (string) $v)
        ->values()
        ->all();

    $criterias = collect($ind['criterias'])->sortBy('sequence')->values()->all();

    $vf = $ind['variable_formula'] ?? [];
    $vfInitial = [
        'variables' => $vf['variables'] ?? [],
        'condition' => !empty($vf['formulas']) ? $vf['formulas'][0]['condition'] ?? '' : '',
        'formula_id' => !empty($vf['formulas']) ? $vf['formulas'][0]['id'] ?? null : null,
    ];

    $checklist = collect($ind['checklistItems']) ?? [];

    function determineScoring($vfInitial, $checklist)
    {
        if ($checklist->count() > 0) {
            return 'selected';
        } else {
            return 'custom';
        }
    }

    $select_scoringMethod = determineScoring($vfInitial, $checklist);
    $scoringMethodFormValue = old('scoring_method', $select_scoringMethod);

    $criteriaInitialItems = [];
    $criteriaInitialNames = [];
    $oldCriteria = old('criteria');

    if (is_array($oldCriteria) && count($oldCriteria)) {
        $index = 0;
        foreach ($oldCriteria as $row) {
            $criteriaInitialItems[] = [
                'id' => isset($row['id']) ? 'criteria_' . $row['id'] : 'old_' . ($index + 1),
                'dbId' => $row['id'] ?? null,
                'name' => $row['name'] ?? '',
                'description' => $row['description'] ?? '',
                'sequence' => $row['sequence'] ?? ($index + 1),
                'data' => $row,
            ];
            $criteriaInitialNames[] = $row['name'] ?? '';
            $index++;
        }
    } else {
        foreach ($criterias as $index => $criteriaRow) {
            $criteriaInitialItems[] = [
                'id' => isset($criteriaRow['id']) ? 'criteria_' . $criteriaRow['id'] : 'criteria_new_' . ($index + 1),
                'dbId' => $criteriaRow['id'] ?? null,
                'name' => $criteriaRow['name'] ?? '',
                'description' => $criteriaRow['description'] ?? '',
                'sequence' => $criteriaRow['sequence'] ?? ($index + 1),
                'data' => $criteriaRow,
            ];
            $criteriaInitialNames[] = $criteriaRow['name'] ?? '';
        }
    }

    if (empty($criteriaInitialItems)) {
        $criteriaInitialItems[] = [
            'id' => 'new_1',
            'dbId' => null,
            'name' => '',
            'description' => '',
            'sequence' => 1,
            'data' => null,
        ];
        $criteriaInitialNames[] = '';
    }

@endphp

@section('content')
    <form action="{{ route('indicator.update', $id) }}" method="POST"
        x-data="{
            score_acc: '',
            scoringMethod: '',
            categoriesByStandard: @js($categoriesByStandard ?? []),
            init() {
                this.score_acc = this.$el.dataset.initialScoreAcc ?? '';
                this.scoringMethod = this.$el.dataset.defaultScoringMethod || '';
                const std = document.getElementById('standard_id')?.value || '';
                const keepCat = document.getElementById('category_id')?.value || '';
                if (std) this.updateCategories(std, keepCat);
            },
            updateCategories(stdId, keepValue = '') {
                const opts = this.categoriesByStandard[String(stdId)] || [];
                const keep = keepValue && opts.some(o => String(o.value) === String(keepValue)) ? keepValue : '';
                window.dispatchEvent(new CustomEvent('select-update-options', {
                    detail: { name: 'category_id', options: opts, value: keep }
                }));
            }
        }"
        @select-change.window="
            if ($event.detail?.name === 'standard_id') {
                updateCategories($event.detail.value, document.getElementById('category_id')?.value || '');
            }
        "
        data-initial-score-acc="{{ old('max_score', $maxScore) }}"
        data-default-scoring-method="{{ $scoringMethodFormValue }}">
        @csrf
        @method('PUT')
        <div class="w-full mx-auto">
            <div class="banner rounded-t-2xl border border-slate-200 p-5">
                <h1 class="text-2xl sm:text-3xl text-center font-bold">แก้ไขตัวบ่งชี้</h1>
            </div>

            <div class="mb-5 w-full px-4 sm:px-6 lg:px-8 py-6 bg-white rounded-b-2xl border border-slate-200 shadow-sm">
                <div class="space-y-6 sm:space-y-8">

                    {{-- Card 1: Basic --}}
                    <x-card number="1" title="ข้อมูลตัวบ่งชี้">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <x-input name="year" type="number" maxlength="4" pattern="\d{4}" :value="$year"
                                label="ปีการประเมิน" placeholder="กรอกปีการประเมิน" required />
                            <x-input name="name" :value="$name" label="ชื่อตัวบ่งชี้"
                                placeholder="กรุณากรอกชื่อตัวบ่งชี้" required />
                            <x-input name="code" :value="$code" label="รหัสตัวบ่งชี้" placeholder="เช่น NCS-1"
                                required />
                            <x-input name="max_score" type="number" step="0.01" :value="$maxScore" label="คะแนนตัวบ่งชี้"
                                placeholder="กรุณากรอกคะแนน" required x-model.number="score_acc" />

                            <x-select name="standard_id" :options="$standards" :value="$std" label="มาตรฐานตัวบ่งชี้"
                                placeholder="กรุณาเลือกมาตรฐานตัวบ่งชี้" required />

                            <x-select name="category_id" :options="$categories" :value="$category" label="ด้านตัวบ่งชี้"
                                placeholder="กรุณาเลือกด้าน" searchable required />

                            <x-select name="type" :options="[
                                'คุณภาพ' => 'คุณภาพ',
                                'ปริมาณ' => 'ปริมาณ',
                                'คุณภาพ/ปริมาณ' => 'คุณภาพ/ปริมาณ',
                            ]" :value="$rawType" label="ประเภทตัวบ่งชี้"
                                placeholder="กรุณาเลือกประเภท" required />

                            <x-input name="deadline" type="date" :value="$deadline" label="วันสิ้นสุดการประเมิน"
                                required />
                        </div>
                    </x-card>

                    {{-- Card 2: Responsible --}}
                    <x-card number="2" title="ผู้รับผิดชอบ">
                        <div x-data="{
                            usersAll: @js($usersForAssign),
                            departmentsAll: @js($departments ?? []),
                            workGroupSelected: (@js(old('work_group_ids', $workGroupSelected)) || []).map(v => String(v)),
                            depSelected: (@js(old('department_ids', $depSelected)) || []).map(v => String(v)),
                            init() {
                                this.$nextTick(() => setTimeout(() => this.refreshDepartmentsAndUsers(), 100));
                            },
                            getMS(el) {
                                if (!el) return null;
                                if (el.__x?.$data) return el.__x.$data;
                                if (typeof Alpine !== 'undefined' && Alpine.$data) return Alpine.$data(el);
                                if (el._x_dataStack?.[0]) return el._x_dataStack[0];
                                return null;
                            },
                            usersEl() { return this.getMS(this.$refs.usersMulti) },
                            deptsEl() { return this.getMS(this.$refs.deptMulti) },
                            filteredDepartments() {
                                if (!this.workGroupSelected.length) return this.departmentsAll;
                                const set = new Set(this.workGroupSelected.map(String));
                                return this.departmentsAll.filter(d => {
                                    const wg = String(d?.work_group ?? '');
                                    return wg && set.has(wg);
                                });
                            },
                            allowedDeptIdsByWorkGroup() {
                                return new Set(this.filteredDepartments().map(d => String(d.id)));
                            },
                            filteredUsers() {
                                if (!this.depSelected.length) return [];
                                const set = new Set(this.depSelected.map(String));
                                return this.usersAll.filter(u => {
                                    const depId = String(u?.department_id ?? u?.department?.id ?? u?.dept_id ?? u?.departmentId ?? '');
                                    return depId && set.has(depId);
                                });
                            },
                            refreshDepartments(openDept = false) {
                                const filtered = this.filteredDepartments();
                                const allowed = new Set(filtered.map(x => String(x.id)));
                                const d = this.deptsEl?.();
                                const keep = (d?.selected || this.depSelected || []).map(String).filter(v => allowed.has(v));
                                this.depSelected = keep;
                                window.dispatchEvent(new CustomEvent('multiselect-update-options', {
                                    detail: { name: 'department_ids', options: filtered, keep, open: openDept }
                                }));
                                if (d) { d.open = true; }
                            },
                            refreshUsers(openUsers = false) {
                                const filtered = this.filteredUsers();
                                const allowed = new Set(filtered.map(x => String(x.id)));
                                const u = this.usersEl?.();
                                const keep = (u?.selected || []).map(String).filter(v => allowed.has(v));

                                window.dispatchEvent(new CustomEvent('multiselect-update-options', {
                                    detail: { name: 'user_ids', options: filtered, keep, open: openUsers || !!(this.depSelected && this.depSelected.length) }
                                }));

                                if (u) { u.open = true; }
                            },
                            refreshDepartmentsAndUsers(openDept = false, openUsers = false) {
                                this.refreshDepartments(openDept);
                                this.refreshUsers(openUsers);
                            }
                        }" x-init="$nextTick(() => setTimeout(() => refreshDepartmentsAndUsers(), 100))"
                            @multiselect-change.window="
                                    if ($event.detail?.name === 'work_group_ids') {
                                        workGroupSelected = ($event.detail.values || []).map(String);
                                        refreshDepartmentsAndUsers(true, false);
                                        $nextTick(() => { document.getElementById('department_ids_search')?.focus(); });
                                    }
                                    if ($event.detail?.name === 'department_ids') {
                                        depSelected = ($event.detail.values || []).map(String);
                                        refreshUsers(true);
                                        // Open users dropdown and focus search
                                        setTimeout(() => {
                                            const u = usersEl?.();
                                            if (u) { u.open = true; }
                                            document.getElementById('user_ids_search')?.focus();
                                        }, 0);
                                    }
                                "
                            class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            {{-- Work groups --}}
                            <x-multiselect name="work_group_ids" label="สังกัดงาน" :options="$affiliations"
                                :value="$workGroupSelected" placeholder="กรุณาเลือกสังกัดงาน" searchable select-all />

                            {{-- Departments --}}
                            <x-multiselect x-ref="deptMulti" name="department_ids" label="หน่วยงานที่รับผิดชอบ"
                                :options="$departments" :value="$depSelected" placeholder="กรุณาเลือกหน่วยงาน" searchable
                                select-all />

                            {{-- Users (filtered by departments) --}}
                            <x-multiselect x-ref="usersMulti" name="user_ids" label="ผู้รับผิดชอบในการรวบรวมข้อมูล"
                                :options="$usersForAssign" optionValue="id" optionLabel="name" :value="$userSelected"
                                placeholder="กรุณาเลือกผู้รับผิดชอบ" searchable select-all required />
                        </div>
                    </x-card>

                    {{-- Card 3: Description --}}
                    <x-card number="3" title="คำอธิบายตัวบ่งชี้">
                        <x-richtext name="description" :value="$desc" placeholder="กรอกคำอธิบายตัวบ่งชี้" />
                    </x-card>

                    {{-- Card 4: Criteria & Condition --}}
                    <x-card number="4" title="เกณฑ์การพิจารณา" class="space-y-6">
                        <x-card-box title="รายการเกณฑ์การพิจารณา" icon="📋">
                            <div x-data="{
                                items: @js($criteriaInitialItems),
                                name: @js($criteriaInitialNames),
                                add() {
                                    const newIndex = this.items.length + 1;
                                    this.items = [...this.items, {
                                        id: 'new_' + Date.now(),
                                        dbId: null,
                                        name: '',
                                        description: '',
                                        sequence: newIndex,
                                        data: null
                                    }];
                                    this.$nextTick(() => this.broadcast())
                                },
                                remove(i) {
                                    const a = [...this.items];
                                    a.splice(i, 1);
                                    // Ensure at least one item exists
                                    this.items = a.length ? a : [{
                                        id: 'new_' + Date.now(),
                                        dbId: null,
                                        name: '',
                                        description: '',
                                        sequence: 1,
                                        data: null
                                    }];
                                    // Update sequences after removal
                                    this.items.forEach((item, index) => {
                                        item.sequence = index + 1;
                                    });
                                    this.$nextTick(() => this.broadcast())
                                },
                                up(i) {
                                    if (i > 0) {
                                        const a = [...this.items];
                                        [a[i - 1], a[i]] = [a[i], a[i - 1]];
                                        // Update sequences after swap
                                        a.forEach((item, index) => {
                                            item.sequence = index + 1;
                                        });
                                        this.items = a;
                                        this.$nextTick(() => this.broadcast())
                                    }
                                },
                                down(i) {
                                    if (i < this.items.length - 1) {
                                        const a = [...this.items];
                                        [a[i + 1], a[i]] = [a[i], a[i + 1]];
                                        // Update sequences after swap
                                        a.forEach((item, index) => {
                                            item.sequence = index + 1;
                                        });
                                        this.items = a;
                                        this.$nextTick(() => this.broadcast())
                                    }
                                },
                                broadcast() {
                                    const inputs = Array.from($el.querySelectorAll('[data-criteria-name]'));
                                    this.name = inputs.map(el => el.value || '');
                                    window.__criteriaTitles = this.name.slice();
                                    $dispatch('criteria-updated', { name: this.name });
                                    window.dispatchEvent(new CustomEvent('criteria-updated', { detail: { name: this.name } }));
                                },
                                updateCriteria(index, field, value) {
                                    if (this.items[index]) {
                                        this.items[index][field] = value;
                                        // Update the data object as well for consistency
                                        if (this.items[index].data) {
                                            this.items[index].data[field] = value;
                                        }
                                    }
                                }
                            }" x-init="$nextTick(() => broadcast())"
                                @criteria-remove="remove($event.detail.index-1)"
                                @criteria-move-up="up($event.detail.index-1)"
                                @criteria-move-down="down($event.detail.index-1)"
                                @criteria-name-change="
                                  const idx = $event.detail.idx;
                                  const newName = $event.detail.name;
                                  name[idx] = newName;
                                  updateCriteria(idx, 'name', newName);
                                  window.__criteriaTitles = name.slice();
                                  $dispatch('criteria-updated', { name })
                                "
                                @criteria-description-change="
                                  const idx = $event.detail.idx;
                                  const newDescription = $event.detail.description;
                                  updateCriteria(idx, 'description', newDescription);
                                "
                                class="space-y-4">
                                <template x-for="(it, i) in items" :key="it.id">
                                    <div x-data="{
                                        sequence: i + 1,
                                        prefix: 'criteria[' + i + ']',
                                        criteriaData: it.data || {
                                            id: it.dbId,
                                            name: it.name,
                                            description: it.description,
                                            sequence: it.sequence,
                                            evidence_requirements: [],
                                            required_evidence_total: null
                                        }
                                    }"
                                        x-effect="sequence = i + 1; prefix = 'criteria[' + i + ']'; it.sequence = sequence;">
                                        <x-card-criteria :show-controls="true" />
                                    </div>
                                </template>

                                <div class="pt-3">
                                    <button type="button" @click="add()" class="btn btn-outline">
                                        <span>เพิ่มเกณฑ์</span>
                                        <span class="text-lg md:text-xl leading-none">＋</span>
                                    </button>
                                </div>
                            </div>
                        </x-card-box>

                        <x-card-box title="วิธีการคำนวณ" icon="📋">
                            <x-richtext name="condition" :value="$cond" placeholder="กรอกวิธีการคำนวณ/เงื่อนไข" />
                        </x-card-box>
                    </x-card>

                    {{-- Card 5: Scoring --}}
                    <x-card number="5" title="เกณฑ์การให้คะแนน" class="space-y-6">
                        <x-card-box icon="📋">
                            <x-slot name="title">
                                <div class="flex items-center space-x-2 font-bold">
                                    <span>เกณฑ์ให้คะแนนและคะแนนเต็ม</span>
                                    <div class="group relative">
                                        <i data-lucide="info" class="w-4 h-4 text-blue-500 cursor-pointer"></i>
                                        <div
                                            class="absolute hidden group-hover:block bg-gray-800 text-white text-xs rounded px-3 py-2 
           bottom-full mb-2 left-1/2 -translate-x-1/2 whitespace-normal z-10 w-72 shadow-lg leading-relaxed">
                                            <p class="font-semibold mb-1">
                                                หมายเหตุ: วิธีกรอกเกณฑ์การให้คะแนน จะต้องมีวงเล็บคะแนน
                                            </p>
                                            <p class="mb-1">ตัวอย่างการกรอก:</p>
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>ได้คะแนนเท่ากับ (10)</li>
                                                <li>ได้คะแนนเท่ากับ (10 คะแนน)</li>
                                                <li>ได้คะแนนเท่ากับ (คะแนน 10)</li>
                                                <li>ได้คะแนนเท่ากับ (คะแนน10)</li>
                                            </ul>
                                        </div>


                                    </div>
                                </div>
                            </x-slot>

                            <div>
                                <x-richtext name="comment" :value="$comment" placeholder="คำอธิบายเกณฑ์ให้คะแนน" />
                                <label for="total_score_display" class="block">
                                    <span class="text-sm font-medium text-slate-700">คะแนนเต็มทั้งหมดของตัวบ่งชี้</span>
                                    <input type="text" id="total_score_display" name="total_score_display" :value="score_acc" readonly
                                        autocomplete="off"
                                        class="p-2 mt-1 w-full bg-gray-100 rounded-xl border border-slate-300 text-sm md:text-base cursor-not-allowed"
                                        placeholder="คะแนนจะปรากฏที่นี่">
                                </label>
                            </div>
                        </x-card-box>

                        <div class="w-full">
                            <p for="scoring_method" class="block mb-3 text-sm font-medium text-slate-700">เลือกวิธีการให้คะแนน</p>
                            <select id="scoring_method" name="scoring_method" x-model="scoringMethod"
                                autocomplete="off"
                                class="p-2 mt-1 w-full bg-white rounded-xl border border-slate-300
                                           placeholder-slate-400 text-sm md:text-base
                                           hover:shadow-md hover:border-blue-400 transition
                                           focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="" @selected($scoringMethodFormValue === '')>-- กรุณาเลือกวิธีการให้คะแนน --</option>
                                <option value="count" @selected($scoringMethodFormValue === 'count')>หลายตัวเลือก - ตามจำนวนข้อที่เลือก</option>
                                <option value="selected" @selected($scoringMethodFormValue === 'selected')>หลายตัวเลือก - คะแนนตามข้อที่เลือก</option>
                                <option value="custom" @selected($scoringMethodFormValue === 'custom')>ปรับแต่งอิสระ</option>
                            </select>
                        </div>

                        <div class="space-y-6">
                            {{-- COUNT rules (prefilled from checklist when possible) --}}
                            <template x-if="scoringMethod === 'count'">
                                <x-card-box title="เกณฑ์ให้คะแนนแบบหลายตัวเลือก-ตามจำนวนข้อที่เลือก" icon="📋">
                                    <x-multichoice-score-count name-prefix="multiCounts" />
                                </x-card-box>
                            </template>

                            {{-- SELECTED rules (UI starts empty; user can recompose as needed) --}}
                            <template x-if="scoringMethod === 'selected'">
                                <x-card-box title="เกณฑ์ให้คะแนนแบบหลายตัวเลือก-คะแนนตามข้อที่เลือก" icon="📋">
                                    <div x-data="{
                                        items: @js(
    count($checklist) > 0
        ? array_map(
            function ($item, $index) {
                return [
                    'id' => 'checklist_' . ($item['id'] ?? 'new_' . ($index + 1)),
                    'dbId' => $item['id'] ?? null,
                    'required_items' => $item['required_items'] ?? [],
                    'score' => $item['score'] ?? 0,
                    'sequence' => $item['sequence'] ?? null,
                    'data' => $item,
                ];
            },
            $checklist
                ->sortBy(function ($item) {
                    return $item['sequence'] ?? -1; // null sequence comes first
                })
                ->values()
                ->all(),
            array_keys(
                $checklist
                    ->sortBy(function ($item) {
                        return $item['sequence'] ?? -1;
                    })
                    ->values()
                    ->all(),
            ),
        )
        : [['id' => 'new_1', 'dbId' => null, 'required_items' => [], 'score' => 0, 'sequence' => null, 'data' => null]],
),
                                        add() {
                                            this.items = [...this.items, {
                                                id: 'new_' + Date.now(),
                                                dbId: null,
                                                required_items: [],
                                                score: 0,
                                                sequence: this.items.length + 1,
                                                data: null
                                            }]
                                        },
                                        remove(i) {
                                            const a = [...this.items];
                                            a.splice(i, 1);
                                            this.items = a.length ? a : [{
                                                id: 'new_' + Date.now(),
                                                dbId: null,
                                                required_items: [],
                                                score: 0,
                                                sequence: 1,
                                                data: null
                                            }];
                                        }
                                    }" @criteria-remove="remove($event.detail.index - 1)"
                                        class="space-y-4">
                                        <template x-for="(it, i) in items" :key="it.id">
                                            <div x-data="{
                                                sequence: i + 1,
                                                prefix: 'multiSelected[' + i + ']',
                                                checklistData: {
                                                    id: it.dbId,
                                                    required_items: it.required_items,
                                                    score: it.score,
                                                    sequence: it.sequence
                                                }
                                            }"
                                                x-effect="sequence = i + 1; prefix = 'multiSelected[' + i + ']';">
                                                <x-multichoice-score-selected />
                                            </div>
                                        </template>

                                        <button type="button" @click="add()" class="btn btn-outline">
                                            <span>เพิ่มเงื่อนไข</span>
                                            <span class="text-lg md:text-xl leading-none">＋</span>
                                        </button>
                                    </div>
                                </x-card-box>
                            </template>

                            {{-- CUSTOM (variables + formula) --}}
                            <template x-if="scoringMethod === 'custom'">
                                <x-card-box title="เกณฑ์ให้คะแนนแบบปรับแต่งอิสระ" icon="📋">
                                    <x-variable-formula prefix="scoring" :initial="$vfInitial" />
                                </x-card-box>
                            </template>
                        </div>
                    </x-card>

                    {{-- Card 6: Note --}}
                    <x-card number="6" title="หมายเหตุ">
                        <x-richtext name="annotation" :value="$note" placeholder="...." />
                    </x-card>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 px-5">
                        <a href="{{ route('indicator.show', $id) }}" class="btn btn-outline">
                            <i class="fa fa-undo"></i>
                            <span>กลับ</span>
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            <span>บันทึกการแก้ไขตัวบ่งชี้</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/justify/trumbowyg.justify.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/trumbowyg.table.min.js"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/ui/trumbowyg.table.min.css">
    <style>
        .banner {
            background: linear-gradient(90deg, #e0f2fe 0%, #fef3e0 100%);
            transition: all 0.3s ease-in-out;
        }

        .trumbowyg-box .trumbowyg-editor-box {
            height: fit-content !important;
        }

        .trumbowyg-box {
            min-height: unset !important;
        }
    </style>
@endpush
