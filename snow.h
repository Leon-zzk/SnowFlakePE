//
// Created by zhangzhikun on 2019/9/10.
//

#ifndef PHPCPP_SNOW_H
#define PHPCPP_SNOW_H

#include <phpcpp.h>
#include <bitset>
#include <string>
#include <sys/time.h>
#include <sstream>

class SnowFlake : public Php::Base {
private:
    static constexpr int64_t _start_time = 1568084685479;
    static constexpr int16_t _worker_id_bits = 10;
    static constexpr int16_t _sequence_bits = 12;
    unsigned long long _sequence_max;
    int16_t _work_id;

    static unsigned long long last_timestamp;
    static unsigned int sequence;

    uint64_t current_time_msc();

    uint64_t next_time_msc(uint64_t last);

public:
    SnowFlake();

    SnowFlake(const SnowFlake &) = delete;

    ~SnowFlake() override = default;

    void __construct(Php::Parameters &params);

    Php::Value gen_id();
};

#endif //PHPCPP_SNOW_H
